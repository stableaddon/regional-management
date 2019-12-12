<?php

namespace Stableaddon\RegionalManagement\Model\ResourceModel\RegionName;

/**
 * Class Collection
 *
 * @package Stableaddon\RegionalManagement\Model\ResourceModel\RegionName
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'region_id';
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init(\Stableaddon\RegionalManagement\Model\RegionName::class, \Stableaddon\RegionalManagement\Model\ResourceModel\RegionName::class);
    }
}
