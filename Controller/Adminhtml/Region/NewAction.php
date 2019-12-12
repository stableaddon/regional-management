<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\Region;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Directory\Model\Config\Source\Country;
use Magento\Directory\Model\Region;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class NewAction
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\Region
 */
class NewAction extends Action
{
    protected $coreRegistry = null;

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Stableaddon_RegionalManagement::region_create';

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
            $this->coreRegistry->register('regional_management_region', $model);
        }

        $countryHelper = $this->_objectManager->get(Country::class);

        $this->coreRegistry->register('regional_management_region_country_list', $countryHelper->toOptionArray());

        return $resultPageFactory;
    }
}
