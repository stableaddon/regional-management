<?php

namespace Stableaddon\RegionalManagement\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

/**
 * Class SubDistrictName
 *
 * @package Stableaddon\RegionalManagement\Model\ResourceModel
 */
class SubDistrictName extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('directory_city_district_name', NULL);
    }
}
