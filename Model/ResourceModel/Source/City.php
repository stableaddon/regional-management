<?php

namespace Stableaddon\RegionalManagement\Model\ResourceModel\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class City
 *
 * @package Stableaddon\RegionalManagement\Model\ResourceModel\Source
 */
class City extends \Magento\Framework\DataObject implements OptionSourceInterface
{
    /**
     * @var \Stableaddon\RegionalManagement\Model\ResourceModel\City\CollectionFactory
     */
    protected $_cityFactory;

    protected $_options;

    /**
     * City constructor.
     *
     * @param \Stableaddon\RegionalManagement\Model\ResourceModel\City\CollectionFactory $cityFactory
     * @param array $data
     */
    public function __construct(
        \Stableaddon\RegionalManagement\Model\ResourceModel\City\CollectionFactory $cityFactory,
        array $data = []
    )
    {
        $this->_cityFactory = $cityFactory;
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
            $this->_options = $this->_createCityCollection()->load()->toOptionArray();
        }

        return $this->_options;
    }

    /**
     * @return mixed
     */
    public function getAllSourceOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->_createCityCollection()->load()->toSourceOptionArray();
        }

        return $this->_options;
    }

    /**
     * @return mixed
     */
    protected function _createCityCollection()
    {
        return $this->_cityFactory->create();
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
