<?php
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
$extConf = $extensionConfiguration->get('addresses');

if ($extConf['addressesInPages']) {
    $tempColumns = array(
        'tx_addresses' => [
            'exclude' => 1,
            'label' => 'Addresses',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_addresses_domain_model_address',
                'MM' => 'tx_addresses_mm',
                'MM_opposite_field' => 'pages',
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
    );
    ExtensionManagementUtility::addTCAcolumns('pages', $tempColumns, 1);
    ExtensionManagementUtility::addToAllTCAtypes(
        'pages',
        '--palette--;Addresses;addresses',
        '1',
        'after:subtitle'
    );
    $GLOBALS['TCA']['pages']['palettes']['addresses']['showitem'] = 'tx_addresses,';
}
