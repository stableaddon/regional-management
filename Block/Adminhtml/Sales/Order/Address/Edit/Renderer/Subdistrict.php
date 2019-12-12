<?php

namespace Stableaddon\RegionalManagement\Block\Adminhtml\Sales\Order\Address\Edit\Renderer;

use Magento\Backend\Block\AbstractBlock;
use Magento\Backend\Block\Context;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Stableaddon\RegionalManagement\Helper\Data;

/**
 * Class Subdistrict
 *
 * @package Stableaddon\RegionalManagement\Block\Adminhtml\Sales\Order\Address\Edit\Renderer
 */
class Subdistrict extends AbstractBlock implements Renderer\RendererInterface
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
     * Subdistrict constructor.
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Stableaddon\RegionalManagement\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        DirectoryHelper $directoryHelper,
        Data $helper,
        array $data = []
    )
    {
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
        $country = $element->getForm()->getElement('country_id');
        $region = $element->getForm()->getElement('region_id');
        $postcode = $element->getForm()->getElement('postcode');
        
        if (!$city = $element->getForm()->getElement('city_id')) {
            return $element->getDefaultHtml();
        }

        $subdistrict = $this->getSubDistrictId();
        $subdistrict = $subdistrict ? $subdistrict : $element->getForm()->getElement('sub_district_id')->getValue();
        $html = '<div class="field field-sub_district required admin__field _required">';
        $element->setClass('input-text admin__control-text');
        $element->setRequired(true);
        $html .= $element->getLabelHtml() . '<div class="control admin__field-control">';
        $html .= $element->getElementHtml();

        $selectName = str_replace('sub_district', 'sub_district_id', $element->getName());
        $selectId = $element->getHtmlId() . '_id';

        $html .= '<select id="' .
            $selectId .
            '" name="' .
            $selectName .
            '" class="select required-entry admin__control-select" style="display:block;">';
        $html .= '<option value="">' . __('Please select a sub district') . '</option>';
        $html .= '</select>';

        $html .= '<script>' . "\n";
        $html .= 'require(["prototype", "Stableaddon_RegionalManagement/js/form"], function(){';
        $html .= '$("' . $selectId . '").setAttribute("defaultValue", "' . $subdistrict . '");' . "\n";
        $html .= 'new SubdistrictUpdater("' .
            $country->getHtmlId() .
            '", "' .
            $region->getHtmlId() .
            '", "' .
            $city->getHtmlId() .
            '", "' .
            $element->getHtmlId() .
            '", "' .
            $selectId .
            '", "' .
            $postcode->getHtmlId() .
            '", ' .
            $this->directoryHelper->getRegionJson() .
            ', ' .
            $this->helper->getSubdistrictJson() .
            ');' .
            '\n';

        $html .= '});';
        $html .= '</script>' . '\n';

        $html .= '</div></div>' . '\n';

        return $html;
    }
}
