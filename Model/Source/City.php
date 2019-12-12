<?php

namespace Stableaddon\RegionalManagement\Model\Source;

/**
 * Class City
 *
 * @package Stableaddon\RegionalManagement\Model\Source
 */
class City extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var \Stableaddon\RegionalManagement\Model\ResourceModel\City\CollectionFactory
     */
    protected $_cityFactory;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionsFactory
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Stableaddon\RegionalManagement\Model\ResourceModel\City\CollectionFactory $cityFactory
    ) {
        $this->_cityFactory = $cityFactory;
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
    }

    /**
     * Retrieve all region options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->_createRegionsCollection()->load()->toOptionArray();
        }
        return $this->_options;
    }

    /**
     * @return \Stableaddon\RegionalManagement\Model\ResourceModel\City\Collection
     */
    protected function _createRegionsCollection()
    {
        return $this->_cityFactory->create();
    }
}
