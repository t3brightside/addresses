<?php
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use Brightside\Addresses\Preview\AddressesPreviewRenderer;

$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['addresses_selected'] =  'mimetypes-x-content-addresses';
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['addresses_frompages'] =  'mimetypes-x-content-addresses';

// Get extension configuration
$extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
$extensionConfiguration = $extensionConfiguration->get('addresses');

// Content element type dropdown
ExtensionManagementUtility::addTcaSelectItem(
    "tt_content",
    "CType",
    [
        'label' => 'Addresses: selected',
        'value' => 'addresses_selected',
        'icon' => 'mimetypes-x-content-addresses',
        'group' => 'default',
        'description' => 'Shows selected address records.',
    ],
    'textmedia',
    'after'
);
ExtensionManagementUtility::addTcaSelectItem(
    "tt_content",
    "CType",
    [
        'label' => 'Addresses: from pages',
        'value' => 'addresses_frompages',
        'icon' => 'mimetypes-x-content-addresses',
        'group' => 'default',
        'description' => 'Shows addresss from selected pages or sys folders.',
    ],
    'textmedia',
    'after'
);

$tempColumns = array(
    'tx_addresses' => [
        'exclude' => 1,
        'label' => 'Selected Adresses',
        'config' => [
            'type' => 'group',
            'internal_type' => 'db',
            'allowed' => 'tx_addresses_domain_model_address',
            'size' => 3,
            'autoSizeMax' => 30,
            'maxitems' => 9999,
            'multiple' => 0,
        ],
    ],
    'tx_addresses_template' => [
        'exclude' => 1,
        'label'   => 'Template',
        'config'  => [
            'type'     => 'select',
            'renderType' => 'selectSingle',
            'default' => 0,
            'items'    => array(), /* items set in page TsConfig */
            'behaviour' => [
                'allowLanguageSynchronization' => true,
            ],
        ],
    ],
    'tx_addresses_orderby' => [
        'exclude' => 1,
        'label'   => 'Order by',
        'config'  => [
            'type'     => 'select',
            'renderType' => 'selectSingle',
            'default' => 0,
            'items' => [
                ['Manual (default)', '0'],
                ['By the sort order', 'sorting ASC'],
                ['By the sort order (reverse)', 'sorting DESC'],
                ['Title (a → z)', 'title ASC'],
                ['Title (z → a)', 'title DESC'],
                ['Last updated (now → past)', 'tstamp DESC'],
                ['Last updated (past → now)', 'tstamp ASC'],
            ],
        ],
    ],
    'tx_addresses_startfrom' => [
        'exclude' => 1,
        'label' => 'Start from Address',
        'config' => [
            'type' => 'input',
            'eval' => 'num',
            'size' => '1',
        ],
    ],
    'tx_addresses_limit' => [
        'exclude' => 1,
        'label' => 'Addresses Shown',
        'config' => [
            'type' => 'input',
            'eval' => 'num',
            'size' => '1',
        ],
    ],
    'tx_addresses_images' => [
        'exclude' => 1,
        'label' => 'Images',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [
                [
                    0 => '',
                    1 => '',
                    'invertStateDisplay' => true
                ]
            ],
        ]
    ],
    'tx_addresses_cropratio' => [
        'exclude' => 1,
        'label'   => 'Image Crop',
        'config'  => [
            'type'     => 'select',
            'renderType' => 'selectSingle',
            'default' => '0',
            'items'    => array(), /* items set in page TsConfig */
        ],
    ],
    'tx_addresses_vcard' => [
        'exclude' => 1,
        'label' => 'vCard Download',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [
                [
                    0 => '',
                    1 => '',
                    'invertStateDisplay' => true
                ]
            ],
        ]
    ],
    'tx_addresses_information' => [
        'exclude' => 1,
        'label' => 'Information',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [
                [
                    0 => '',
                    1 => '',
                    'invertStateDisplay' => true
                ]
            ],
        ]
    ],
    'tx_addresses_titlewrap' => [
        'exclude' => 1,
        'label'   => 'Name Wrap',
        'config'  => [
            'type'     => 'select',
            'renderType' => 'selectSingle',
            'default' => 0,
            'items'    => array(), /* items set in page TsConfig */
            'behaviour' => [
                'allowLanguageSynchronization' => true,
            ],
        ],
    ],
);

ExtensionManagementUtility::addTCAcolumns('tt_content', $tempColumns);
$GLOBALS['TCA']['tt_content']['types']['addresses_selected']['previewRenderer'] = AddressesPreviewRenderer::class;
$GLOBALS['TCA']['tt_content']['types']['addresses_selected']['showitem'] = '
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
        --palette--;;general,
        --palette--;;headers,
        --palette--;Data;addressesSelectedData,
    	--palette--;Layout;addressesLayout,
    --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
        --palette--;;frames,
        --palette--;;appearanceLinks,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
        --palette--;;language,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
        --palette--;;hidden,
        --palette--;;access,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
        categories,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
        rowDescription,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
';
if ($extensionConfiguration['addressesEnablePagination']) {
    $GLOBALS['TCA']['tt_content']['types']['addresses_selected']['showitem'] = str_replace(
        ';addressesLayout,',
        ';addressesLayout,
		--palette--;Pagination;paginatedprocessors,',
        $GLOBALS['TCA']['tt_content']['types']['addresses_selected']['showitem']
    );
}

$GLOBALS['TCA']['tt_content']['types']['addresses_frompages']['previewRenderer'] = AddressesPreviewRenderer::class;
$GLOBALS['TCA']['tt_content']['types']['addresses_frompages']['showitem'] = '
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
        --palette--;;general,
        --palette--;;headers,
        --palette--;Data;addressesFrompagesData,
    	--palette--;Layout;addressesLayout,
    	--palette--;Filter;addressesFilters,
    --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
        --palette--;;frames,
        --palette--;;appearanceLinks,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
        --palette--;;language,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
        --palette--;;hidden,
        --palette--;;access,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
        categories,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
        rowDescription,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
';
if ($extensionConfiguration['addressesEnablePagination']) {
    $GLOBALS['TCA']['tt_content']['types']['addresses_frompages']['showitem'] = str_replace(
        ';addressesFilters,',
        ';addressesFilters,
		--palette--;Pagination;paginatedprocessors,',
        $GLOBALS['TCA']['tt_content']['types']['addresses_frompages']['showitem']
    );
}

$GLOBALS['TCA']['tt_content']['palettes']['addressesSelectedData']['showitem'] = '
	tx_addresses,
';
$GLOBALS['TCA']['tt_content']['palettes']['addressesFrompagesData']['showitem'] = '
	pages,
	--linebreak--,
	tx_addresses_orderby,
	tx_addresses_startfrom,
	tx_addresses_limit,
';
$GLOBALS['TCA']['tt_content']['palettes']['addressesFilters']['showitem'] = '
	selected_categories;by Category,
';
$GLOBALS['TCA']['tt_content']['palettes']['addressesLayout']['showitem'] = '
	tx_addresses_template,
    tx_addresses_titlewrap,
	tx_addresses_images,
    tx_addresses_cropratio,--linebreak--,
    tx_imagelazyload,
	tx_addresses_information,
';
