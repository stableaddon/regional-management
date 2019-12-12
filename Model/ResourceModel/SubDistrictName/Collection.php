<?php

namespace Stableaddon\RegionalManagement\Model\ResourceModel\SubDistrictName;

/**
 * Class Collection
 *
 * @package Stableaddon\RegionalManagement\Model\ResourceModel\SubDistrictName
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init(\Stableaddon\RegionalManagement\Model\SubDistrictName::class, \Stableaddon\RegionalManagement\Model\ResourceModel\SubDistrictName::class);
    }
}
