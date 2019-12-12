<?php

namespace Stableaddon\RegionalManagement\Model\Import;

use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Stableaddon\RegionalManagement\Model\Import\Validator\RowValidatorInterface as ValidatorInterface;

/**
 * Class Region
 *
 * @package Stableaddon\RegionalManagement\Model\Import
 */
class Region extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    const DEFAULT_NAME = 'default_name';
    const CODE = 'code';
    const COUNTRY = 'country_id';
    const TABLE_Entity = 'directory_country_region';

    /**
     * Json Serializer Instance
     *
     * @var Json
     */
    private $serializer;

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
        ValidatorInterface::ERROR_TITLE_IS_EMPTY => 'TITLE is empty',
    ];

    protected $_permanentAttributes = [self::DEFAULT_NAME];

    /**
     * If we should check column names
     *
     * @var bool
     */
    protected $needColumnCheck = true;

    protected $groupFactory;

    /**
     * Valid column names
     *
     * @array
     */
    protected $validColumnNames = [
        self::DEFAULT_NAME,
        self::CODE,
        self::COUNTRY
    ];

    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;

    protected $_validators = [];

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_connection;

    protected $_resource;

    protected $_countryCollection;

    protected $_countryData;

    /**
     * Region constructor.
     *
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\ImportExport\Helper\Data $importExportData
     * @param \Magento\ImportExport\Model\ResourceModel\Import\Data $importData
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface $errorAggregator
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollection
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        CountryCollectionFactory $countryCollection

    )
    {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->_resource = $resource;
        $this->_connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->_countryCollection = $countryCollection;
        $this->_countryData = $countryCollection->create()->toOptionArray();
    }

    public function getValidColumnNames()
    {
        return $this->validColumnNames;
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'directory_country_region';
    }

    /**
     * Row validation.
     *
     * @param array $rowData
     * @param int $rowNum
     *
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }
        $this->_validatedRows[$rowNum] = true;
        if (!isset($rowData[self::DEFAULT_NAME]) || empty($rowData[self::DEFAULT_NAME])) {
            $this->addRowError(ValidatorInterface::ERROR_TITLE_IS_EMPTY, $rowNum);

            return false;
        }

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * Create Advanced price data from raw data.
     *
     * @throws \Exception
     * @return bool Result of operation.
     */
    protected function _importData()
    {
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            $this->deleteEntity();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            $this->replaceEntity();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $this->getBehavior()) {
            $this->saveEntity();
        }

        return true;
    }

    /**
     * Validate data rows and save bunches to DB.
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _saveValidatedBunches()
    {
        $source = $this->_getSource();
        $currentDataSize = 0;
        $bunchRows = [];
        $startNewBunch = false;
        $nextRowBackup = [];
        $maxDataSize = $this->_resourceHelper->getMaxDataSize();
        $bunchSize = $this->_importExportData->getBunchSize();
        $skuSet = [];

        $source->rewind();
        $this->_dataSourceModel->cleanBunches();

        while ($source->valid() || $bunchRows) {
            if ($startNewBunch || !$source->valid()) {
                $this->_dataSourceModel->saveBunch($this->getEntityTypeCode(), $this->getBehavior(), $bunchRows);

                $bunchRows = $nextRowBackup;
                $currentDataSize = strlen($this->getSerializer()->serialize($bunchRows));
                $startNewBunch = false;
                $nextRowBackup = [];
            }
            if ($source->valid()) {
                try {
                    $rowData = $source->current();
                } catch (\InvalidArgumentException $e) {
                    $this->addRowError($e->getMessage(), $this->_processedRowsCount);
                    $this->_processedRowsCount++;
                    $source->next();
                    continue;
                }

                $this->_processedRowsCount++;

                if ($this->validateRow($rowData, $source->key())) {
                    $rowData = $this->_prepareRowForDb($rowData);
                    $rowSize = strlen($this->jsonHelper->jsonEncode($rowData));

                    $isBunchSizeExceeded = $bunchSize > 0 && count($bunchRows) >= $bunchSize;

                    if ($currentDataSize + $rowSize >= $maxDataSize || $isBunchSizeExceeded) {
                        $startNewBunch = true;
                        $nextRowBackup = [$source->key() => $rowData];
                    } else {
                        $bunchRows[$source->key()] = $rowData;
                        $currentDataSize += $rowSize;
                    }
                }
                $source->next();
            }
        }
        $this->_processedEntitiesCount = count($skuSet);

        return $this;
    }

    /**
     * Get Serializer instance
     *
     * Workaround. Only way to implement dependency and not to break inherited child classes
     *
     * @return Json
     * @deprecated 100.2.0
     */
    private function getSerializer()
    {
        if (null === $this->serializer) {
            $this->serializer = ObjectManager::getInstance()->get(Json::class);
        }

        return $this->serializer;
    }

    /**
     * Save newsletter subscriber
     *
     * @return $this
     */
    public function saveEntity()
    {
        $this->saveAndReplaceEntity();

        return $this;
    }

    /**
     * Replace newsletter subscriber
     *
     * @return $this
     */
    public function replaceEntity()
    {
        $this->saveAndReplaceEntity();

        return $this;
    }

    /**
     * Deletes newsletter subscriber data from raw data.
     *
     * @return $this
     */
    public function deleteEntity()
    {
        $listTitle = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);
                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $rowTtile = $rowData[self::DEFAULT_NAME];
                    $listTitle[] = $rowTtile;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }
        if ($listTitle) {
            $this->deleteEntityFinish(array_unique($listTitle), self::TABLE_Entity);
        }

        return $this;
    }

    /**
     * Save and replace newsletter subscriber
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function saveAndReplaceEntity()
    {
        $behavior = $this->getBehavior();
        $listTitle = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(ValidatorInterface::ERROR_TITLE_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
                $key = array_search($rowData[self::COUNTRY], array_column($this->_countryData, 'label'));
                if ($key) {
                    $rowTtile = $rowData[self::DEFAULT_NAME];
                    $listTitle[] = $rowTtile;
                    $entityList[$rowTtile][] = [
                        self::COUNTRY => $this->_countryData[$key]['value'],
                        self::CODE => $rowData[self::CODE],
                        self::DEFAULT_NAME => $rowData[self::DEFAULT_NAME]
                    ];
                }
            }
            if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
                if ($listTitle) {
                    if ($this->deleteEntityFinish(array_unique($listTitle), self::TABLE_Entity)) {
                        $this->saveEntityFinish($entityList, self::TABLE_Entity);
                    }
                }
            } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $behavior) {
                $this->saveEntityFinish($entityList, self::TABLE_Entity);
            }
        }

        return $this;
    }

    /**
     * Save product prices.
     *
     * @param array $priceData
     * @param string $table
     *
     * @return $this
     */
    protected function saveEntityFinish(array $entityData, $table)
    {
        if ($entityData) {
            $tableName = $this->_connection->getTableName($table);
            $entityIn = [];
            foreach ($entityData as $id => $entityRows) {
                foreach ($entityRows as $row) {
                    $entityIn[] = $row;
                }
            }
            if ($entityIn) {
                $this->_connection->insertOnDuplicate($tableName, $entityIn, [
                    self::COUNTRY,
                    self::CODE,
                    self::DEFAULT_NAME
                ]);
            }
        }

        return $this;
    }

    protected function deleteEntityFinish(array $listTitle, $table)
    {
        if ($table && $listTitle) {
            try {
                $this->countItemsDeleted += $this->_connection->delete(
                    $this->_connection->getTableName($table),
                    $this->_connection->quoteInto('default_name IN (?)', $listTitle)
                );

                return true;
            } catch (\Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }
}