<?php
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addToInsertRecords('tx_addresses_domain_model_address');

return [
    'ctrl' => [
        'title' => 'Addresses',
        'label' => 'name',
        'label_alt' => 'address',
        'label_alt_force' => 1,
        'prependAtCopy' => true,
        'hideAtCopy' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'editlock' => 'editlock',
        'type' => '0',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'default_sortby' => 'ORDER BY sorting ASC',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group',
        ],
        'iconfile' => 'EXT:addresses/Resources/Public/Icons/mimetypes-x-content-addresses.svg',
        'searchFields' => 'uid,name,address,city,zip,region,country,email,phone,fax,website,info',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;Address,
                --palette--;Address;address,
                --palette--;Contact;contact,
                --palette--;Images;media,
                --palette--;Social;social,
                --palette--;Geolocation;geo,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;paletteAccess,
                --div--;Language,
                --palette--;;paletteLanguage,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
                selected_categories,
                --div--;Pages,pages,

            '
        ]
    ],
    'palettes' => [
        'address' => [
            'showitem' => '
                name,--linebreak--,
                address,
                city,
                zip,
                region,--linebreak--,
                country,
            '
        ],
        'geo' => [
            'showitem' => '
                latitude,
                longitude,--linebreak--,
                timezone,
            '
        ],
        'contact' => [
            'showitem' => '
                email,
                phone,
                fax,
                website,--linebreak--,
                info,
            '
        ],
        'media' => [
            'showitem' => '
                images,--linebreak--,
            '
        ],
        'social' => [
            'showitem' => '
                linkedin,
                xing,
                x,
                github,
                instagram,
                youtube,
                facebook,
            '
        ],
        'paletteAccess' => [
            'showitem' => '
                hidden,--linebreak--,starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel,
                endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel,
                --linebreak--, fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel,
                --linebreak--,fe_login_mode,editlock,
            ',
        ],
        'paletteLanguage' => [
            'showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource,',
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ],
                ],
                'default' => 0,
            ]
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
                'default' => ''
            ]
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'Address visible',
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
            ],
        ],
        'cruser_id' => [
            'label' => 'cruser_id',
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'pid' => [
            'label' => 'pid',
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'crdate' => [
            'label' => 'crdate',
            'config' => [
                'type' => 'passthrough',
            ]
        ],
        'tstamp' => [
            'label' => 'tstamp',
            'config' => [
                'type' => 'passthrough',
            ]
        ],
        'sorting' => [
            'label' => 'sorting',
            'config' => [
                'type' => 'passthrough',
            ]
        ],
        'starttime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel',
            'config' => [
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
                'default' => 0,
            ]
        ],
        'endtime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel',
            'config' => [
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
                'default' => 0,
            ]
        ],
        'fe_group' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.fe_group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'size' => 5,
                'maxitems' => 20,
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.hide_at_login',
                        -1,
                    ],
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.any_login',
                        -2,
                    ],
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.usergroups',
                        '--div--',
                    ],
                ],
                'exclusiveKeys' => '-1,-2',
                'foreign_table' => 'fe_groups',
                'foreign_table_where' => 'ORDER BY fe_groups.title',
            ],
        ],
        'editlock' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:editlock',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                    ]
                ],
            ]
        ],
        'selected_categories' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:selected_categories',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectTree',
                'foreign_table' => 'sys_category',
                'foreign_table_where' => 'AND sys_category.sys_language_uid IN (0,-1) ORDER BY sys_category.title ASC',
                'size' => 20,
                'treeConfig' => [
                    'parentField' => 'parent',
                        'appearance' => [
                        'expandAll' => true,
                        'showHeader' => true,
                    ],
                ],
                'MM' => 'sys_category_record_mm',
                'MM_match_fields' => [
                    'fieldname' => 'categories',
                    'tablenames' => 'tx_addresses_domain_model_address',
                ],
                'MM_opposite_field' => 'items',
                'foreign_table' => 'sys_category',
                'foreign_table_where' => ' AND (sys_category.sys_language_uid = 0 OR sys_category.l10n_parent = 0) ORDER BY sys_category.sorting',
                'size' => 10,
                'minitems' => 0,
                'maxitems' => 99,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],

        'name' => [
            'exclude' => 1,
            'label' => 'Name',
            'config' => [
                'type' => 'input',
                'size' => 100,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'address' => [
            'exclude' => 1,
            'label' => 'Address',
            'config' => [
                'type' => 'text',
                'size' => 20,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'city' => [
            'exclude' => 1,
            'label' => 'City',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'zip' => [
            'exclude' => 1,
            'label' => 'Postal Code',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'size' => 20,
            ]
        ],
        'region' => [
            'exclude' => 1,
            'label' => 'Region',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'country' => [
            'exclude' => 1,
            'label' => 'Country',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'latitude' => [
            'exclude' => 1,
            'label' => 'Latitude',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'longitude' => [
            'exclude' => 1,
            'label' => 'Longitude',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'timezone' => [
            'label' => 'Timezone',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'itemsProcFunc' => 'Brightside\\Addresses\\Userfunc\\TimezoneOptions->getTimezones', // Reference the custom method
                'size' => 1,
                'maxitems' => 1,
                'items' => [
                    ['', ''], // Add this to provide an empty first option
                ],
            ],
        ],
        'phone' => [
            'exclude' => 1,
            'label' => 'Phone',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'fax' => [
            'exclude' => 1,
            'label' => 'Fax',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'email' => [
            'exclude' => 1,
            'label' => 'E-mail',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim,email',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'images' => [
            'exclude' => 1,
            'label' => 'Image',
            'config' => [
                'type' => 'file',
                'maxitems' => 100,
                'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
                ],
                'overrideChildTca' => [
                    'columns' => [
                        'crop' => [
                            'config' => [
                                'cropVariants' => [
                                    'default' => [
                                        'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.crop_variant.default',
                                        'allowedAspectRatios' => [

                                            'NaN' => [
                                                'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
                                                'value' => 0.0
                                            ],
                                        ],
                                        'selectedRatio' => 'NaN',
                                        'cropArea' => [
                                            'x' => 0.0,
                                            'y' => 0.0,
                                            'width' => 1.0,
                                            'height' => 1.0,
                                        ],
                                    ],
                                    'tv' => [
                                        'title' => 'TV (4:3)',
                                        'selectedRatio' => '4:3',
                                        'allowedAspectRatios' => [
                                            '4:3' => [
                                                'title' => 'TV',
                                                'value' => 4 / 3,
                                            ],
                                        ],
                                    ],
                                    'widescreen' => [
                                        'title' => 'Widescreen (16:9)',
                                        'selectedRatio' => '16:9',
                                        'allowedAspectRatios' => [
                                            '16:9' => [
                                                'title' => 'Widescreen',
                                                'value' => 16 / 9,
                                            ],
                                        ],
                                    ],
                                    'anamorphic' => [
                                        'title' => 'Anamorphic (2.39:1)',
                                        'selectedRatio' => '2.39:1',
                                        'allowedAspectRatios' => [
                                            '2.39:1' => [
                                                'title' => 'Anamorphic',
                                                'value' => 2.39 / 1,
                                            ],
                                        ],
                                    ],
                                    'square' => [
                                        'title' => 'Square (1:1)',
                                        'selectedRatio' => '1:1',
                                        'allowedAspectRatios' => [
                                            '1:1' => [
                                                'title' => 'Square',
                                                'value' => 1 / 1,
                                            ],
                                        ],
                                    ],
                                    'portrait' => [
                                        'title' => 'Portrait (3:4)',
                                        'selectedRatio' => '3:4',
                                        'allowedAspectRatios' => [
                                            '3:4' => [
                                                'title' => 'Portrait (three-four)',
                                                'value' => 3 / 4,
                                            ],
                                        ],
                                    ],
                                    'tower' => [
                                        'title' => 'Tower (9:16)',
                                        'selectedRatio' => '9:16',
                                        'allowedAspectRatios' => [
                                            '9:16' => [
                                                'title' => 'Tower',
                                                'value' => 9 / 16,
                                            ],
                                        ],
                                    ],
                                    'skyscraper' => [
                                        'title' => 'Skyscraper (1:2.39)',
                                        'selectedRatio' => '1:2.39',
                                        'allowedAspectRatios' => [
                                            '1:2.39' => [
                                                'title' => 'Skyscraper',
                                                'value' => 1 / 2.39,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'types' => [
                        '0' => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                    ],
                ],
            ],
        ],
        'info' => [
            'exclude' => 1,
            'l10n_mode' => 'prefixLangTitle',
            'label' => 'Additional Information',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'cols' => 60,
                'rows' => 6
            ]
        ],
        'linkedin' => [
            'exclude' => 1,
            'label' => 'LinkedIn',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'xing' => [
            'exclude' => 1,
            'label' => 'Xing',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'x' => [
            'exclude' => 1,
            'label' => 'X',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'github' => [
            'exclude' => 1,
            'label' => 'GitHub',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'instagram' => [
            'exclude' => 1,
            'label' => 'Instagram',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'youtube' => [
            'exclude' => 1,
            'label' => 'YouTube',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'facebook' => [
            'exclude' => 1,
            'label' => 'Facebook',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'website' => [
            'exclude' => 1,
            'label' => 'Website',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'pages' => [
            'exclude' => 1,
            'label' => 'Pages',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'pages',
                'MM' => 'tx_addresses_mm',
                'MM_match_fields' => [
                    'tablenames' => 'pages',
                    'fieldname' => 'tx_addresses',
                ],
                'size' => 5,
                'autoSizeMax' => 5,
                'maxitems' => 9999,
                'multiple' => 0,
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => true,
                        'options' => [
                            'windowOpenParameters' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
                        ],
                    ],
                    'addRecord' => [
                        'disabled' => true,
                    ],
                    'listModule' => [
                        'disabled' => true,
                    ],
                ],
            ],
        ],
    ],
];
