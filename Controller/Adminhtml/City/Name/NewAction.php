<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\City\Name;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Config\Model\Config\Source\Locale;
use Stableaddon\RegionalManagement\Model\Source\City;
use Magento\Backend\Model\Session;
use Magento\Directory\Model\Region;

/**
 * Class NewAction
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\City\Name
 */
class NewAction extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $coreRegistry = null;

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Stableaddon_RegionalManagement::city_name_create';

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultPageFactory;

    /**
     * NewAction constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Create new Region
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultPageFactory = $this->resultPageFactory->create();

        $FormData = $this->_objectManager->get(Session::class)->getFormData(true);
        if (!empty($FormData)) {
            $model = $this->_objectManager->create(Region::class);
            $model->setData($FormData);
            $this->coreRegistry->register('regional_management_city_name', $model);
        }

        $countryHelper = $this->_objectManager->get(Locale::class);
        $this->coreRegistry->register('regional_management_city_name_locale_list', $countryHelper->toOptionArray());
        $regionHelper = $this->_objectManager->get(City::class);
        $this->coreRegistry->register('regional_management_city_name_region_list', $regionHelper->toOptionArray());

        return $resultPageFactory;
    }
}
