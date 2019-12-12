<?php

namespace Stableaddon\RegionalManagement\Block\Adminhtml;

/**
 * Class RegionName
 *
 * @package Stableaddon\RegionalManagement\Block\Adminhtml
 */
class RegionName extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Stableaddon_RegionalManagement';
        $this->_controller = 'adminhtml_region_name';
        $this->_headerText = __('Regions Name Manager');
        $this->_addButtonLabel = __('Add New Region Name');
        parent::_construct();
    }
}
