<?php

namespace Stableaddon\RegionalManagement\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

/**
 * Class RegionName
 *
 * @package Stableaddon\RegionalManagement\Model\ResourceModel
 */
class RegionName extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('directory_country_region_name', NULL);
    }
}
