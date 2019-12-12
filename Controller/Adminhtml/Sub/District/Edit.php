<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\Sub\District;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Stableaddon\RegionalManagement\Model\ResourceModel\Source\City as CitySource;
use Stableaddon\RegionalManagement\Model\SubDistrict;

/**
 * Class Edit
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\Sub\District
 */
class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Stableaddon_RegionalManagement::city_edit';

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
    )
    {
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
        // Get ID and create model
        $id = (int)$this->getRequest()->getParam('id');
        $model = $this->_objectManager->create(SubDistrict::class);
        $model->setData([]);
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This sub district no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
            $defaultName = $model->getName();
        }

        $FormData = $this->_objectManager->get(Session::class)->getFormData(true);
        if (!empty($FormData)) {
            $model->setData($FormData);
        }

        $this->_coreRegistry->register('regional_management_sub_district', $model);

        $countryHelper = $this->_objectManager->get(CitySource::class);

        $this->_coreRegistry->register('regional_management_sub_district_city_list', $countryHelper->toOptionArray());

        // Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit City') : __('New Sub District'),
            $id ? __('Edit City') : __('New Sub District')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Sub District Manager'));
        $resultPage->getConfig()->getTitle()
            ->prepend($id ? __('Edit: %1 (%2)', $defaultName, $id) : __('New Sub District'));

        return $resultPage;
    }
}
