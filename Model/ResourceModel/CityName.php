<?php

namespace Stableaddon\RegionalManagement\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

/**
 * Class CityName
 *
 * @package Stableaddon\RegionalManagement\Model\ResourceModel
 */
class CityName extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('directory_region_city_name', NULL);
    }
}
