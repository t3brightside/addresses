<?php
namespace Brightside\Addresses\Hooks;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ContentPostProcessor
{
    /**
     * @param $funcRef
     */
    public function render($params)
    {
        /** @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $feobj */
        $feobj = &$params['pObj'];
        if ($GLOBALS['TSFE']->type == 888) {
            $addressId = (int) GeneralUtility::_GET('address');
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_addresses_domain_model_address');
            $statement = $queryBuilder
                ->select('*')
                ->from('tx_addresses_domain_model_address')
                ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($addressId, \PDO::PARAM_INT)))
                ->executeQuery();
            $vcfFilename = 'address.vcf';
            while ($row = $statement->fetch()) {
                if ($row['title']) {
                    $vcfFilename = $row['title'] . '.vcf';
                }
            }
            header('Content-type: text/x-vCard; charset=utf-8');
            header('Content-Disposition: attachment; ; filename="'.$vcfFilename.'"');
        }
    }
}
