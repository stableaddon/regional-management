<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\Region;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Magento\Directory\Model\Region;
use Magento\Backend\Model\Session;
use Magento\Directory\Model\Config\Source\Country;

class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Stableaddon_RegionalManagement::region_edit';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Edit constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Customer::customer');
        return $resultPage;
    }

    /**
     * Edit Region page
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('region_id');
        $model = $this->_objectManager->create(Region::class);
        $model->setData([]);

        if ($id && $id > 0) {
            $model->load($id);
            if (!$model->getRegionId()) {
                $this->messageManager->addError(__('This region no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
            $defaultName = $model->getDefaultName();
        }

        $FormData = $this->_objectManager->get(Session::class)->getFormData(true);
        if (!empty($FormData)) {
            $model->setData($FormData);
        }

        $this->_coreRegistry->register('regional_management_region', $model);
        $countryHelper = $this->_objectManager->get(Country::class);
        $this->_coreRegistry->register('regional_management_region_country_list', $countryHelper->toOptionArray());

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Region') : __('New Region'),
            $id ? __('Edit Region') : __('New Region')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Regions Manager'));
        $resultPage->getConfig()->getTitle()
            ->prepend($id ? __('Edit: %1 (%2)', $defaultName, $id) : __('New Region'));

        return $resultPage;
    }
}
