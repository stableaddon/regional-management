<?php

namespace Stableaddon\RegionalManagement\Block\Adminhtml;

/**
 * Class City
 *
 * @package Stableaddon\RegionalManagement\Block\Adminhtml
 */
class City extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Stableaddon_RegionalManagement';
        $this->_controller = 'adminhtml_city';
        $this->_headerText = __('City/District Manager');
        $this->_addButtonLabel = __('Add New City/District');
        parent::_construct();
    }
}
