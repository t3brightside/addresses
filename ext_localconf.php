<?php
declare(strict_types=1);

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use Brightside\Addresses\Hooks\ContentPostProcessor;

defined('TYPO3') || die('Access denied.');

(function () {
    $versionInformation = GeneralUtility::makeInstance(Typo3Version::class);
    // Only include page.tsconfig if TYPO3 version is below 12 so that it is not imported twice.
    if ($versionInformation->getMajorVersion() < 13) {
        TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
            @import "EXT:addresses/Configuration/TSConfig/wizard.tsconfig"
        ');
    }

    $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
    $iconRegistry->registerIcon(
        'mimetypes-x-content-addresses',SvgIconProvider::class,
        ['source' => 'EXT:addresses/Resources/Public/Icons/mimetypes-x-content-addresses.svg']
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] =
    ContentPostProcessor::class . '->render';

    ExtensionUtility::configurePlugin(
        'addresses',
        'Addresses',
        [
            'Addresses' => 'addresses'
        ]
    );
})();
