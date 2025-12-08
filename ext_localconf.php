<?php
declare(strict_types=1);

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die('Access denied.');

(function () {
    ExtensionUtility::configurePlugin(
        'addresses',
        'Addresses',
        [
            'Addresses' => 'addresses'
        ],
        [], 
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT 
    );
})();
