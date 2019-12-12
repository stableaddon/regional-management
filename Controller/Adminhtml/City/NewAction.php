<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\City;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Customer\Model\ResourceModel\Address\Attribute\Source\Region;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Stableaddon\RegionalManagement\Model\City;

/**
 * Class NewAction
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\City
 */
class NewAction extends Action
{
    protected $coreRegistry = null;

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Stableaddon_RegionalManagement::city_create';

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
            $model = $this->_objectManager->create(City::class);
            $model->setData($FormData);
            $this->coreRegistry->register('regional_management_city', $model);
        }

        $countryHelper = $this->_objectManager->get(Region::class);

        $this->coreRegistry->register('regional_management_city_region_list', $countryHelper->toOptionArray());

        return $resultPageFactory;
    }
}
