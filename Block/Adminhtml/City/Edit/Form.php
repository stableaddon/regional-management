<?php

namespace Stableaddon\RegionalManagement\Block\Adminhtml\City\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * Class Form
 *
 * @package Stableaddon\RegionalManagement\Block\Adminhtml\City\Edit
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
        $this->setId('city_form');
        $this->setTitle(__('City/District Information'));
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

        $form->setHtmlIdPrefix('city_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('General Information'),
                'class' => 'fieldset-wide'
            ]
        );

        $region_list = $this->_coreRegistry->registry('regional_management_city_region_list');

        $fieldset->addField(
            'region_id',
            'select',
            [
                'name' => 'region_id',
                'label' => __('Province'),
                'title' => __('Province'),
                'required' => true,
                'values' => $region_list
            ]
        );

        $fieldset->addField(
            'default_name',
            'text',
            [
                'name' => 'default_name',
                'label' => __('City/District Name'),
                'title' => __('City/District Name'),
                'required' => true
            ]
        );

        $formData = $this->_coreRegistry->registry('regional_management_city');
        if ($formData) {
            if ($formData->getRegionId()) {
                $fieldset->addField(
                    'city_id',
                    'hidden',
                    ['name' => 'city_id']
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
