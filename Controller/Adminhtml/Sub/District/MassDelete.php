<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\Sub\District;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Stableaddon\RegionalManagement\Component\MassAction\Filter;
use Stableaddon\RegionalManagement\Model\ResourceModel\SubDistrict\CollectionFactory;
use Stableaddon\RegionalManagement\Model\SubDistrict;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Stableaddon_RegionalManagement::sub_district_delete';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $ids = $this->filter->getFilterIds();

        foreach ($ids as $id) {
            $model = $this->_objectManager->create(SubDistrict::class);
            $model->load($id);
            $model->delete();
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', count($ids)));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
