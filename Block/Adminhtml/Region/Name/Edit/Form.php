<?php

namespace Stableaddon\RegionalManagement\Block\Adminhtml\Region\Name\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * Class Form
 *
 * @package Stableaddon\RegionalManagement\Block\Adminhtml\Region\Name\Edit
 */
class Form extends Generic
{
    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * Form constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('region_name_form');
        $this->setTitle(__('Region Name Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post'
                ]
            ]
        );

        $form->setHtmlIdPrefix('region_name_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('General Information'),
                'class' => 'fieldset-wide'
            ]
        );

        $country_list = $this->_coreRegistry->registry('regional_management_region_name_locale_list');

        $fieldset->addField(
            'locale',
            'select',
            [
                'name' => 'locale',
                'label' => __('Locale'),
                'title' => __('Locale'),
                'required' => true,
                'values' => $country_list
            ]
        );

        $region_list = $this->_coreRegistry->registry('regional_management_region_name_region_list');

        $fieldset->addField(
            'region_id',
            'select',
            [
                'name' => 'region_id',
                'label' => __('Region'),
                'title' => __('Region'),
                'required' => true,
                'values' => $region_list
            ]
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true
            ]
        );

        $formData = $this->_coreRegistry->registry('regional_management_region_name');
        if ($formData) {
            $form->setValues($formData->getData());
        }

        $form->setUseContainer(true);
        $form->setMethod('post');
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
