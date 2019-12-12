<?php

namespace Stableaddon\RegionalManagement\Block\Adminhtml\Sales\Order\Address\Edit\Renderer;

use Magento\Backend\Block\AbstractBlock;
use Magento\Backend\Block\Context;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Stableaddon\RegionalManagement\Helper\Data as HelperData;

/**
 * Class City
 *
 * @package Stableaddon\RegionalManagement\Block\Adminhtml\Sales\Order\Address\Edit\Renderer
 */
class City extends AbstractBlock implements RendererInterface
{
    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * @var \Stableaddon\RegionalManagement\Helper\Data
     */
    protected $helper;

    /**
     * City constructor.
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Stableaddon\RegionalManagement\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        DirectoryHelper $directoryHelper,
        HelperData $helper,
        array $data = []
    ) {
        $this->directoryHelper = $directoryHelper;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Output the region element and javasctipt that makes it dependent from country element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function render(AbstractElement $element)
    {
        if (!$region = $element->getForm()->getElement('region_id')) {
            return $element->getDefaultHtml();
        }

        if (!$country = $element->getForm()->getElement('country_id')) {
            return $element->getDefaultHtml();
        }

        $cityId = $this->getCityId();

        $html = '<div class="field field-city required admin__field _required">';
        $element->setClass('input-text admin__control-text');
        $element->setRequired(true);
        $html .= $element->getLabelHtml() . '<div class="control admin__field-control">';
        $html .= $element->getElementHtml();

        $selectName = str_replace('city', 'city_id', $element->getName());
        $selectId = $element->getHtmlId() . '_id';
        $html .= '<select id="' .
            $selectId .
            '" name="' .
            $selectName .
            '" class="select required-entry admin__control-select" style="display:block;">';
        $html .= '<option value="">' . __('Please select a city/district') . '</option>';
        $html .= '</select>';

        $html .= '<script>' . "\n";
        $html .= 'require(["prototype", "Stableaddon_RegionalManagement/js/form"], function(){';
        $html .= '$("' . $selectId . '").setAttribute("defaultValue", "' . $cityId . '");' . "\n";
        $html .= 'new CityUpdater("' .
            $country->getHtmlId() .
            '", "' .
            $region->getHtmlId() .
            '", "' .
            $element->getHtmlId() .
            '", "' .
            $selectId .
            '", ' .
            $this->helper->getCityJson() .
            ', ' .
            $this->directoryHelper->getRegionJson() .
            ');' .
            '\n';

        $html .= '});';
        $html .= '</script>' . '\n';

        $html .= '</div></div>' . '\n';

        return $html;
    }
}
