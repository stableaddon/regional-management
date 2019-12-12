<?php

namespace Stableaddon\RegionalManagement\Block\Adminhtml\Order\Address;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Form
 *
 * @package Stableaddon\RegionalManagement\Block\Adminhtml\Order\Address
 */
class Form extends \Magento\Sales\Block\Adminhtml\Order\Address\Form
{
    /**
     * Add additional data to form element
     *
     * @param AbstractElement $element
     * @return $this
     */
    protected function _addAdditionalFormElementData(AbstractElement $element)
    {
        if ($element->getId() == 'region_id') {
            $element->setNoDisplay(true);
        }
        if ($element->getId() == 'city_id') {
            $element->setNoDisplay(true);
        }
        if ($element->getId() == 'sub_district_id') {
            $element->setNoDisplay(true);
        }
        return $this;
    }

    /**
     * @return array
     */
    protected function _getAdditionalFormElementRenderers()
    {
        return [
            'region' => $this->getLayout()->createBlock(
                \Magento\Customer\Block\Adminhtml\Edit\Renderer\Region::class
            ),
            'city' => $this->getLayout()->createBlock(
                \Stableaddon\RegionalManagement\Block\Adminhtml\Sales\Order\Address\Edit\Renderer\City::class
            ),
            'sub_district' => $this->getLayout()->createBlock(
                \Stableaddon\RegionalManagement\Block\Adminhtml\Sales\Order\Address\Edit\Renderer\Subdistrict::class
            )
        ];
    }
}
