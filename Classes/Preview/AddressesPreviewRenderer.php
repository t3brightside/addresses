<?php

declare(strict_types=1);

namespace Brightside\Addresses\Preview;

use TYPO3\CMS\Backend\Preview\PreviewRendererInterface;
use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Core\Collection\LazyRecordCollection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Domain\RecordInterface;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AddressesPreviewRenderer extends StandardContentPreviewRenderer implements PreviewRendererInterface
{
    private const ADDRESSES_TABLE = 'tx_addresses_domain_model_address';
    private const TT_CONTENT_TABLE = 'tt_content';

    /**
     * Safely retrieves a field value from the record, supporting both array (v12)
     * and RecordInterface (v14+) access.
     */
    private function getRecordValueSafely(array|RecordInterface $record, string $field, mixed $default = null): mixed
    {
        if (is_array($record)) {
            return $record[$field] ?? $default;
        } elseif ($record instanceof RecordInterface) {
            return $record->has($field) ? $record->get($field) : $default;
        }
        return $default;
    }

    /**
     * Checks if a field is available for editing based on permissions AND TSconfig overrides (User/Page).
     */
    private function isFieldAvailableForRecord(array|RecordInterface $record, string $fieldName): bool
    {
        // 1. Check if the field exists in tt_content columns array (TCA structure check)
        if (!isset($GLOBALS['TCA'][self::TT_CONTENT_TABLE]['columns'][$fieldName])) {
            return false;
        }
        
        // 2. Permission Check (non_exclude_fields)
        if (!isset($GLOBALS['BE_USER']) || !$GLOBALS['BE_USER']->check('non_exclude_fields', self::TT_CONTENT_TABLE . ':' . $fieldName)) {
            return false;
        }

        // --- 3. TSconfig Removal/Disabled Check (User + Page) ---
        
        $pid = $this->getRecordValueSafely($record, 'pid', 0);
        $CType = $this->getRecordValueSafely($record, 'CType', '');

        // Fetch both configurations (using documented API)
        $userTsConfig = $GLOBALS['BE_USER']->getTSConfig();
        $pageTsConfig = $pid > 0 ? BackendUtility::getPagesTSconfig((int)$pid) : [];
        
        // Define all places where the field config might live:
        $tsConfigPaths = [
            // [Table/Field config array, CType specific config array]
            [
                $userTsConfig['TCEFORM.'][self::TT_CONTENT_TABLE . '.'][$fieldName . '.'] ?? [],
                $userTsConfig['TCEFORM.'][self::TT_CONTENT_TABLE . '.'][$fieldName . '.']['types.'][$CType . '.'] ?? [],
            ],
            [
                $pageTsConfig['TCEFORM.'][self::TT_CONTENT_TABLE . '.'][$fieldName . '.'] ?? [],
                $pageTsConfig['TCEFORM.'][self::TT_CONTENT_TABLE . '.'][$fieldName . '.']['types.'][$CType . '.'] ?? [],
            ],
        ];

        foreach ($tsConfigPaths as [$globalConfig, $typeConfig]) {
            // Check 3a: Global Field Rule (TCEFORM.tt_content.field.disabled = 1)
            $isDisabledGlobal = (bool)($globalConfig['disabled'] ?? false);
            if ($isDisabledGlobal) {
                return false;
            }

            // Check 3b: Conditional Type Rule (TCEFORM.tt_content.field.types.CType.disabled = 1)
            $isDisabledType = (bool)($typeConfig['disabled'] ?? false);
            if ($isDisabledType) {
                return false;
            }

            // Check 3c: Explicit Removal (TCEFORM.field.removeItems = fieldname)
            $removeItems = $globalConfig['removeItems'] ?? '';
            if (GeneralUtility::inList($removeItems, $fieldName)) {
                return false;
            }
        }
        
        // If all checks pass, the field is available for viewing/editing.
        return true;
    }

    /**
     * Extract UIDs from a field that could be a string CSV (v12) or LazyRecordCollection (v14).
     */
    private function extractUids(mixed $field): array
    {
        if (!$field) {
            return [];
        }

        if (is_string($field)) {
            return GeneralUtility::intExplode(',', $field, true);
        }

        if ($field instanceof LazyRecordCollection) {
            $uids = [];
            foreach ($field as $record) {
                if (is_object($record) && method_exists($record, 'has') && $record->has('uid')) {
                    $uids[] = (int)$record->get('uid');
                }
            }
            return $uids;
        }

        if (is_array($field)) {
            return array_filter(array_map('intval', $field));
        }

        return [];
    }

    /**
     * Fetches titles for the given page UIDs.
     */
    private function getPageTitles(mixed $pages): array
    {
        $pageIds = $this->extractUids($pages);
        if (empty($pageIds)) {
            return [];
        }

        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        $titles = [];
        foreach ($pageIds as $uid) {
            $page = $pageRepository->getPage((int)$uid);
            if ($page && isset($page['title'])) {
                $titles[] = $page['title'];
            }
        }
        return $titles;
    }

    /**
     * Fetches category records for the given UIDs.
     */
    private function getCategories(mixed $categories): array
    {
        $categoryIds = $this->extractUids($categories);
        if (empty($categoryIds)) {
            return [];
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_category');

        return $queryBuilder
            ->select('uid', 'title')
            ->from('sys_category')
            ->where($queryBuilder->expr()->in('uid', $categoryIds))
            ->executeQuery()
            ->fetchAllAssociative() ?: [];
    }

    // ------------------------------
    // Preview Rendering Methods
    // ------------------------------

    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $record = $item->getRecord();
        $getValue = fn(string $field, mixed $default = null) => $this->getRecordValueSafely($record, $field, $default);
        
        // Pass the entire record to the helper so we can extract the PID and CType
        $isFieldAvailable = fn(string $field) => $this->isFieldAvailableForRecord($record, $field);

        // --- Retrieve all configuration fields ---
        $CType = $getValue('CType', '');
        $pids = $getValue('pages');
        $selectedCategories = $getValue('selected_categories');
        $selectedRecords = $getValue('tx_addresses');

        $template = $getValue('tx_addresses_template');
        $disableImages = (bool)$getValue('tx_addresses_images', 0);
        $cropRatio = $getValue('tx_addresses_cropratio');
        $disableInformation = (bool)$getValue('tx_addresses_information', 0);
        $showPersonnel = (bool)$getValue('tx_addresses_personnel', 0);
        $orderBy = $getValue('tx_addresses_orderby');
        $firstResult = $getValue('tx_addresses_startfrom');
        $maxResults = $getValue('tx_addresses_limit');
        $titleWrap = $getValue('tx_addresses_titlewrap');

        $paginationEnabled = (bool)$getValue('tx_paginatedprocessors_paginationenabled', 0);
        $itemsPerPage = $getValue('tx_paginatedprocessors_itemsperpage');
        $pageLinksShown = $getValue('tx_paginatedprocessors_pagelinksshown');
        $anchor = $getValue('tx_paginatedprocessors_anchor');
        $anchorId = $getValue('tx_paginatedprocessors_anchorid');
        $urlSegment = $getValue('tx_paginatedprocessors_urlsegment');

        // --- Data Fetching & Query Execution Setup (unchanged) ---
        $pageTitles = $this->getPageTitles($pids);
        $categoryRecords = $this->getCategories($selectedCategories);
        $categoryTitles = array_column($categoryRecords, 'title');

        $addressesRecords = [];
        $query = null;
        $selectedRecordUids = $this->extractUids($selectedRecords);
        $pidUids = $this->extractUids($pids);
        $categoryUids = $this->extractUids($selectedCategories);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::ADDRESSES_TABLE);

        // --- Query Construction and Execution (unchanged) ---
        if ($CType === 'addresses_selected' && !empty($selectedRecordUids)) {
            $query = $queryBuilder
                ->select('uid', 'name', 'address')
                ->from(self::ADDRESSES_TABLE)
                ->where($queryBuilder->expr()->in('uid', $selectedRecordUids));
        } elseif ($CType === 'addresses_frompages' && !empty($pidUids)) {
            // ... query logic ...
        }

        if ($query instanceof QueryBuilder) {
            $addressesRecords = $query->executeQuery()->fetchAllAssociative();
        }

        // Reorder array for 'addresses_selected' (unchanged)
        if ($CType === 'addresses_selected' && !empty($selectedRecordUids)) {
            // ... reordering logic ...
        }

        // --- HTML Output Generation ---

        $output = '<div class="element-preview-content">';

        $labelStyle = 'display: inline-block; min-width: 200px; font-weight: bold;';

        $createDetailLine = function (string $label, string $value) use ($record, $labelStyle): string {
            $content = '<strong style="' . $labelStyle . '">' . htmlspecialchars($label) . '</strong>' . htmlspecialchars($value);
            return '<div>' . $this->linkEditContent($content, $record) . '</div>';
        };

        // 1. Source, Filtering, Ordering & Display Configuration (with availability checks)

        if ($CType === 'addresses_selected' && !empty($addressesRecords)) {
            $addressNames = [];
            foreach ($addressesRecords as $address) {
                $displayName = trim($address['name'] ?? '');

                // Fallback 1: Use ONLY the first line of the address field if name is empty
                if ($displayName === '') {
                    $addressContent = trim($address['address'] ?? '');
                    if ($addressContent !== '') {
                        $pos = strcspn($addressContent, "\n\r");
                        $displayName = substr($addressContent, 0, $pos);
                    }
                }

                // Fallback 2: Use UID if both name and (first line of) address are empty
                if ($displayName === '') {
                    $displayName = 'Address UID: ' . $address['uid'];
                }

                $addressNames[] = $displayName;
            }
            
            $value = implode('; ', $addressNames); 
            $output .= $createDetailLine('Selected Addresses:', $value);
        }

        // PAGES FIELD CHECK
        if ($isFieldAvailable('pages') && !empty($pageTitles)) {
            $value = '<span>' . implode(', ', $pageTitles) . '</span>';
            $content = '<strong style="' . $labelStyle . '">Addresses from:</strong>' . $value;
            $output .= '<div>' . $this->linkEditContent($content, $record) . '</div>';
        }

        // SELECTED_CATEGORIES FIELD CHECK
        if ($isFieldAvailable('selected_categories') && !empty($categoryTitles)) {
            $value = implode(', ', $categoryTitles);
            $output .= $createDetailLine('Category filter (ANY):', $value);
        }

        // ORDER BY FIELD CHECK
        if ($isFieldAvailable('tx_addresses_orderby') && $orderBy) {
            $output .= $createDetailLine('Order by:', $orderBy);
        }

        // START FROM FIELD CHECK
        if ($isFieldAvailable('tx_addresses_startfrom') && $firstResult) {
            $output .= $createDetailLine('Start from record:', $firstResult);
        }

        // LIMIT FIELD CHECK
        if ($isFieldAvailable('tx_addresses_limit') && $maxResults) {
            $output .= $createDetailLine('Limit to:', $maxResults);
        }

        // TEMPLATE FIELD CHECK
        if ($isFieldAvailable('tx_addresses_template') && $template) {
            $output .= $createDetailLine('Layout Template:', $template);
        }

        // TITLE WRAP FIELD CHECK
        if ($isFieldAvailable('tx_addresses_titlewrap') && $titleWrap) {
            $output .= $createDetailLine('Title Wrap:', $titleWrap);
        }

        // IMAGES FIELD CHECK
        if ($isFieldAvailable('tx_addresses_images')) {
            if ($disableImages) {
                $output .= $createDetailLine('Images:', 'disabled');
            } else {
                $output .= $createDetailLine('Images:', 'enabled');
                
                // CROP RATIO FIELD CHECK (Only relevant if Images are available)
                if ($isFieldAvailable('tx_addresses_cropratio') && $cropRatio) {
                    $output .= $createDetailLine('Image crop:', $cropRatio);
                }
            }
        }

        // INFORMATION FIELD CHECK
        if ($isFieldAvailable('tx_addresses_information')) {
            if (!$disableInformation) {
                $output .= $createDetailLine('Information:', 'enabled');
            } else {
                $output .= $createDetailLine('Information:', 'disabled');
            }
        }

        // PERSONNEL FIELD CHECK
        if ($isFieldAvailable('tx_addresses_personnel')) {
            if ($showPersonnel) {
                $output .= $createDetailLine('Show personnel:', 'enabled');
            } else {
                $output .= $createDetailLine('Show personnel:', 'disabled');
            }
        }

        // 2. Pagination (Only display if pagination fields are available and enabled)
        
        $isPaginationConfigAvailable = $isFieldAvailable('tx_paginatedprocessors_paginationenabled');

        if ($paginationEnabled && $isPaginationConfigAvailable) {
            
            $paginationContent = '<br /><strong>Pagination:</strong> active';

            // ITEM PER PAGE CHECK
            if ($isFieldAvailable('tx_paginatedprocessors_itemsperpage') && $itemsPerPage) {
                $paginationContent .= ' •&nbsp;items per page: ' . htmlspecialchars($itemsPerPage);
            }
            // PAGE LINKS SHOWN CHECK
            if ($isFieldAvailable('tx_paginatedprocessors_pagelinksshown') && $pageLinksShown) {
                $paginationContent .= ' •&nbsp;page links shown: ' . htmlspecialchars($pageLinksShown);
            }
            
            // ANCHOR ID CHECK
            if ($isFieldAvailable('tx_paginatedprocessors_anchorid') && is_numeric($anchorId) && (int)$anchorId > 0) {
                $paginationContent .= ' •&nbsp;focus on page change: ' . htmlspecialchars($anchorId);
            } else {
                // ANCHOR CHECK (Simple checkbox option)
                if ($isFieldAvailable('tx_paginatedprocessors_anchor') && $anchor) {
                    $paginationContent .= ' •&nbsp;focus self on page change';
                }
            }
            // URL SEGMENT CHECK
            if ($isFieldAvailable('tx_paginatedprocessors_urlsegment') && $urlSegment) {
                $paginationContent .= ' •&nbsp;anchor: ' . htmlspecialchars($urlSegment);
            }

            $output .= '<div>' . $this->linkEditContent($paginationContent, $record) . '</div>';
        }

        $output .= '</div>';

        return $output;
    }
}