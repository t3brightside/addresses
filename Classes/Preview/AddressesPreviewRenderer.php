<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Brightside\Addresses\Preview;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;


class AddressesPreviewRenderer extends StandardContentPreviewRenderer
{
    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $extensionConfiguration = $extensionConfiguration->get('addresses');

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(
            'EXT:addresses/Resources/Private/Templates/Backend/Preview.html',
        );

        $record = $item->getRecord();
        $view->assign('addressesitem', $record);

        // Initialize an array to store addresses records
        $addressesRecords = [];

        $CType = $record['CType'];
        $pids = $record['pages'];
        $orderBy = !empty($record['tx_addresses_orderby']) ? $record['tx_addresses_orderby'] : null;
        $maxResults = !empty($record['tx_addresses_limit']) ? intval($record['tx_addresses_limit']) : null;
        $firstResult = !empty($record['tx_addresses_startfrom']) ? intval($record['tx_addresses_startfrom']) : null;
        $selectedCategories = $record['selected_categories'];
        $selectedRecords = $record['tx_addresses'];

        // Query for selected addresses records
        if ($CType == 'addresses_selected' && $selectedRecords) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_addresses_domain_model_address');
            $query = $queryBuilder
                ->select('*')
                ->from('tx_addresses_domain_model_address')
                ->where(
                    $queryBuilder->expr()->in('uid', $selectedRecords)
                );
        }

        // Query for addresses records from selected pages/sysfolders

        if ($CType == 'addresses_frompages' && $pids) {
            // Get titles of selected startingpoints
            if ($record['pages']) {
                $pageIds = explode(',', $record['pages']);
                $pageIds = array_map('intval', $pageIds);
                $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
                $pageTitles = [];
                foreach ($pageIds as $pageUid) {
                    $pageData = $pageRepository->getPage($pageUid);
                    if ($pageData && isset($pageData['title'])) {
                        $pageTitles[] = $pageData['title'];
                    }
                }
                $view->assign('pageTitles', $pageTitles);
            }


            // Get selected catefories
            if ($selectedCategories) {
                $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_addresses_domain_model_address');
                $query = $queryBuilder
                    ->select('title')
                    ->from('sys_category')
                    ->where(
                        $queryBuilder->expr()->in('uid', $selectedCategories)
                    );
                $queryResult = $query->executeQuery();
                $cetegoryTitles = $queryResult->fetchAllAssociative();
                $view->assign('catTitles', $cetegoryTitles);
            }

            // Fetch addresses records with the given page UID
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_addresses_domain_model_address');
            $query = $queryBuilder
                ->select('tx_addresses_domain_model_address.*')
                ->from('tx_addresses_domain_model_address')
                ->leftJoin(
                    'tx_addresses_domain_model_address',
                    'sys_category_record_mm',
                    'category_mm',
                    $queryBuilder->expr()->eq('tx_addresses_domain_model_address.uid', 'category_mm.uid_foreign')
                );
            // Select only from certain categories
            if ($selectedCategories) {
                $query->where(
                    $queryBuilder->expr()->and(
                        $queryBuilder->expr()->in('tx_addresses_domain_model_address.pid', $pids),
                        $queryBuilder->expr()->in('category_mm.uid_local', $selectedCategories)
                    )
                );
            } else {
                $query->where(
                    $queryBuilder->expr()->in('tx_addresses_domain_model_address.pid', $pids)
                );
            }

            $query->groupBy('tx_addresses_domain_model_address.uid');

            if($orderBy) {
                [$column, $direction] = explode(' ', $orderBy, 2);
                $query->orderBy($column,$direction);
            }

            if($maxResults) {
                $query->setMaxResults($maxResults);
            }

            if($firstResult) {
                $query->setFirstResult($firstResult);
            }
        }

        // Query execution
        if ($selectedRecords || $pids){
            $query->executeQuery();
            $queryResult = $query->executeQuery();
            $addressesRecords = $queryResult->fetchAllAssociative();
        }



        // Reorder array to sort by selected records order
        if (
            $CType == 'addresses_selected' &&
            $selectedRecords
        ) {
            $addressesRecordsReindex = [];
            foreach ($addressesRecords as $item) {
                $addressesRecordsReindex[$item['uid']] = $item;
            }
            $defaultSorting = array_flip(GeneralUtility::intExplode(",", $selectedRecords));
            $addressesRecordsSortedCleaned = array_filter(array_replace($defaultSorting, $addressesRecordsReindex), function($item) {
                return !is_int($item);
            });
            if(count($addressesRecordsSortedCleaned)){
                $addressesRecords = $addressesRecordsSortedCleaned;
                unset($addressesRecordsReindex,$addressesRecordsSortedCleaned);
            }
        }
        if($extensionConfiguration['addressesBeThumbnails']) {
            // Get images for addresss
            foreach ($addressesRecords as &$addressesRecord) {
                if (!empty($addressesRecord['images'])) {
                    $fileReferences = BackendUtility::resolveFileReferences('tx_addresses_domain_model_address', 'images', $addressesRecord);
                    // Limit to the first image only
                    if (!empty($fileReferences)) {
                        $firstImageReference = reset($fileReferences); // Get the first element of the array
                        $addressesRecord['resolvedImages'] = [$firstImageReference]; // Store in a new array
                    }
                }
            }
        }

        // Assign to template
        if ($selectedRecords || $pids){
            $view->assign('addressesRecords', $addressesRecords);
        }
        $out = $view->render();
        return $this->linkEditContent($out, $record);
    }
}
