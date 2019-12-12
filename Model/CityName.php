<?php

namespace Stableaddon\RegionalManagement\Model;

/**
 * Class CityName
 *
 * @package Stableaddon\RegionalManagement\Model
 */
class CityName extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'directory_region_city_name';
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Stableaddon\RegionalManagement\Model\ResourceModel\CityName::class);
    }
}
