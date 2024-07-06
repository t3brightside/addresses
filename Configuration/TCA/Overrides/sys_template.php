<?php
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addStaticFile(
    'addresses',
    'Configuration/TypoScript/',
    'Addresses'
);

ExtensionManagementUtility::addStaticFile(
    'addresses',
    'Configuration/TypoScript/NewsAuthor',
    'Addresses - News author'
);
