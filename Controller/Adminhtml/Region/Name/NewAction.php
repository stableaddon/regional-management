<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\Region\Name;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Config\Model\Config\Source\Locale;
use Magento\Customer\Model\ResourceModel\Address\Attribute\Source\Region as RegionSource;
use Magento\Directory\Model\Region;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class NewAction
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\Region\Name
 */
class NewAction extends \Magento\Backend\App\Action
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
        Context $context,
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

        $countryHelper = $this->_objectManager->get(Locale::class);

        $this->coreRegistry->register('regional_management_region_name_locale_list', $countryHelper->toOptionArray());

        $regionHelper = $this->_objectManager->get(RegionSource::class);

        $this->coreRegistry->register('regional_management_region_name_region_list', $regionHelper->toOptionArray());

        return $resultPageFactory;
    }
}
