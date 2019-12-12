<?php

namespace Stableaddon\RegionalManagement\Model\Source;

/**
 * Class SubDistrict
 *
 * @package Stableaddon\RegionalManagement\Model\Source
 */
class SubDistrict extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var \Stableaddon\RegionalManagement\Model\ResourceModel\City\CollectionFactory
     */
    protected $_cityFactory;

    /**
     * SubDistrict constructor.
     *
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \Stableaddon\RegionalManagement\Model\ResourceModel\SubDistrict\CollectionFactory $cityFactory
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Stableaddon\RegionalManagement\Model\ResourceModel\SubDistrict\CollectionFactory $cityFactory
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
     * @return mixed
     */
    protected function _createRegionsCollection()
    {
        return $this->_cityFactory->create();
    }
}
