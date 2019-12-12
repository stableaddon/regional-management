<?php

namespace Stableaddon\RegionalManagement\Model;

/**
 * Class RegionName
 *
 * @package Stableaddon\RegionalManagement\Model
 */
class RegionName extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'directory_country_region_name';
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Stableaddon\RegionalManagement\Model\ResourceModel\RegionName::class);
    }
}
