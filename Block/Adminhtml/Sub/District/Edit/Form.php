<?php

namespace Stableaddon\RegionalManagement\Block\Adminhtml\Sub\District\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;

/**
 * Class Form
 *
 * @package Stableaddon\RegionalManagement\Block\Adminhtml\Sub\District\Edit
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
        $this->setId('sub_district_form');
        $this->setTitle(__('Sub District Information'));
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

        $form->setHtmlIdPrefix('sub_district_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('General Information'),
                'class' => 'fieldset-wide'
            ]
        );

        $region_list = $this->_coreRegistry->registry('regional_management_sub_district_city_list');

        $fieldset->addField(
            'city_id',
            'select',
            [
                'name' => 'city_id',
                'label' => __('City/District'),
                'title' => __('City/District'),
                'required' => true,
                'values' => $region_list
            ]
        );

        $fieldset->addField(
            'default_name',
            'text',
            [
                'name' => 'default_name',
                'label' => __('Sub District Name'),
                'title' => __('Sub District Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'postcode',
            'text',
            [
                'name' => 'postcode',
                'label' => __('Postcode'),
                'title' => __('Postcode'),
                'required' => true
            ]
        );

        $formData = $this->_coreRegistry->registry('regional_management_sub_district');
        if ($formData) {
            if ($formData->getId()) {
                $fieldset->addField(
                    'district_id',
                    'hidden',
                    ['name' => 'district_id']
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
