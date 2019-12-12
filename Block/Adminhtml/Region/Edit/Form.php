<?php

namespace Stableaddon\RegionalManagement\Block\Adminhtml\Region\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * Class Form
 *
 * @package Stableaddon\RegionalManagement\Block\Adminhtml\Region\Edit
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
        $this->setId('region_form');
        $this->setTitle(__('State/Region/Province Information'));
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

        $form->setHtmlIdPrefix('region_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('General Information'),
                'class' => 'fieldset-wide'
            ]
        );

        $country_list = $this->_coreRegistry->registry('regional_management_region_country_list');

        $fieldset->addField(
            'country_id',
            'select',
            [
                'name' => 'country_id',
                'label' => __('Country'),
                'title' => __('Country'),
                'required' => true,
                'values' => $country_list
            ]
        );

        $fieldset->addField(
            'code',
            'text',
            [
                'name' => 'code',
                'label' => __('State/Region/Province Code'),
                'title' => __('State/Region/Province Code'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'default_name',
            'text',
            [
                'name' => 'default_name',
                'label' => __('State/Region/Province Name'),
                'title' => __('State/Region/Province Name'),
                'required' => true
            ]
        );

        $formData = $this->_coreRegistry->registry('regional_management_region');
        if ($formData) {
            if ($formData->getRegionId()) {
                $fieldset->addField(
                    'region_id',
                    'hidden',
                    ['name' => 'region_id']
                );
            }
            $form->setValues($formData->getData());
        }

        $form->setUseContainer(true);
        $form->setMethod('post');
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
