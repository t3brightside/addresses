<?php
namespace Brightside\Addresses\DataProcessing;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\DataProcessing\DatabaseQueryProcessor;
use Brightside\Paginatedprocessors\Processing\DataToPaginatedData;

class AddressesDatabaseQueryProcessor extends DatabaseQueryProcessor
{
    /**
     * Fetches records from the database as an array
     *
     * @param ContentObjectRenderer $cObj
     * @param array $contentObjectConfiguration
     * @param array $processorConfiguration
     * @param array $processedData
     * @return array
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        // Conditional processing
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        // Table to query
        $tableName = $cObj->stdWrapValue('table', $processorConfiguration);
        if (empty($tableName)) {
            return $processedData;
        }

        unset($processorConfiguration['table.'], $processorConfiguration['table']);

        // Target variable for Fluid
        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'records');

        // Fetch records
        $records = $cObj->getRecords($tableName, $processorConfiguration);

        $processedRecordVariables = [];
        foreach ($records as $record) {
            /** @var ContentObjectRenderer $recordCObj */
            $recordCObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $recordCObj->start($record, $tableName);

            $processedRecordVariables[$record['uid']] = ['data' => $record];
            $processedRecordVariables[$record['uid']] = $this->contentDataProcessor->process(
                $recordCObj,
                $processorConfiguration,
                $processedRecordVariables[$record['uid']]
            );
        }

        // Manual sorting
        $manualSorting = array_flip(GeneralUtility::intExplode(",", $cObj->data['tx_addresses']));
        $processedRecordVariablesSortedCleaned = array_filter(
            array_replace($manualSorting, $processedRecordVariables),
            fn($item) => !is_int($item)
        );

        if (count($processedRecordVariablesSortedCleaned)) {
            $processedRecordVariables = $processedRecordVariablesSortedCleaned;
        }

        $processedData[$targetVariableName] = $processedRecordVariables;

        // Parent processing
        $allProcessedData = parent::process($cObj, $contentObjectConfiguration, $processorConfiguration, $processedData);

        // Pagination
        $paginationSettings = $processorConfiguration['pagination.'] ?? [];
        if ((int)($cObj->stdWrapValue('isActive', $paginationSettings))) {
            $paginatedData = new DataToPaginatedData();
            $allProcessedData = $paginatedData->getPaginatedData(
                $cObj,
                $contentObjectConfiguration,
                $processorConfiguration,
                $allProcessedData,
                $allProcessedData[$processorConfiguration['as']],
                $processorConfiguration['as']
            );
        }

        return $allProcessedData;
    }
}