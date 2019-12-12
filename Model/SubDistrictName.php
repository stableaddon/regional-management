<?php

namespace Stableaddon\RegionalManagement\Model;

/**
 * Class SubDistrictName
 *
 * @package Stableaddon\RegionalManagement\Model
 */
class SubDistrictName extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'directory_city_district_name';
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Stableaddon\RegionalManagement\Model\ResourceModel\SubDistrictName::class);
    }
}
