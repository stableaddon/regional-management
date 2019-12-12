<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\Sub\District;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Stableaddon\RegionalManagement\Model\ResourceModel\Source\City as CitySource;
use Stableaddon\RegionalManagement\Model\SubDistrict;

/**
 * Class NewAction
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\Sub\District
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
     * @return mixed
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultPageFactory = $this->resultPageFactory->create();

        $FormData = $this->_objectManager->get(Session::class)->getFormData(true);
        if (!empty($FormData)) {
            $model = $this->_objectManager->create(SubDistrict::class);
            $model->setData($FormData);
            $this->coreRegistry->register('regional_management_sub_district', $model);
        }

        $countryHelper = $this->_objectManager->get(CitySource::class);

        $this->coreRegistry->register('regional_management_sub_district_city_list', $countryHelper->toOptionArray());

        return $resultPageFactory;
    }
}
