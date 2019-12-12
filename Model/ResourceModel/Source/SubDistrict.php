<?php

namespace Stableaddon\RegionalManagement\Model\ResourceModel\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class SubDistrict
 *
 * @package Stableaddon\RegionalManagement\Model\ResourceModel\Source
 */
class SubDistrict extends \Magento\Framework\DataObject implements OptionSourceInterface
{
    /**
     * @var \Stableaddon\RegionalManagement\Model\ResourceModel\City\CollectionFactory
     */
    protected $_subDistrictFactory;

    protected $_options;

    public function __construct(
        \Stableaddon\RegionalManagement\Model\ResourceModel\SubDistrict\CollectionFactory $subDistrictFactory,
        array $data = []
    )
    {
        $this->_subDistrictFactory = $subDistrictFactory;
        parent::__construct($data);
    }

    /**
     * Retrieve all region options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->_createSubDistrictCollection()->load()->toOptionArray();
        }

        return $this->_options;
    }

    /**
     * @return mixed
     */
    protected function _createSubDistrictCollection()
    {
        return $this->_subDistrictFactory->create();
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
