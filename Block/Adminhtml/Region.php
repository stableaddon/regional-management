<?php

namespace Stableaddon\RegionalManagement\Block\Adminhtml;

/**
 * Class Region
 *
 * @package Stableaddon\RegionalManagement\Block\Adminhtml
 */
class Region extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Stableaddon_RegionalManagement';
        $this->_controller = 'adminhtml_region';
        $this->_headerText = __('Regions Manager');
        $this->_addButtonLabel = __('Add New Region');
        parent::_construct();
    }
}
