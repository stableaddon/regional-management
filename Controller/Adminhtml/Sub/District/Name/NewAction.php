<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\Sub\District\Name;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Config\Model\Config\Source\Locale;
use Magento\Directory\Model\Region;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Stableaddon\RegionalManagement\Model\Source\City;

/**
 * Class NewAction
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\Sub\District\Name
 */
class NewAction extends Action
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
    const ADMIN_RESOURCE = 'Stableaddon_RegionalManagement::sub_district_name_create';

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
        Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory
    )
    {
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
            $this->coreRegistry->register('regional_management_sub_district_name', $model);
        }

        $countryHelper = $this->_objectManager->get(Locale::class);
        $this->coreRegistry->register('regional_management_sub_district_name_locale_list', $countryHelper->toOptionArray());
        $regionHelper = $this->_objectManager->get(City::class);
        $this->coreRegistry->register('regional_management_sub_district_name_region_list', $regionHelper->toOptionArray());

        return $resultPageFactory;
    }
}
