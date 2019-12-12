<?php

namespace Stableaddon\RegionalManagement\Model\ResourceModel\CityName;

/**
 * Class Collection
 *
 * @package Stableaddon\RegionalManagement\Model\ResourceModel\CityName
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init(\Stableaddon\RegionalManagement\Model\CityName::class, \Stableaddon\RegionalManagement\Model\ResourceModel\CityName::class);
    }
}
