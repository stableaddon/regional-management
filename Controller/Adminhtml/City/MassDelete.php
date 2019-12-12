<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\City;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Stableaddon\RegionalManagement\Component\MassAction\Filter;
use Stableaddon\RegionalManagement\Model\City;
use Stableaddon\RegionalManagement\Model\ResourceModel\City\CollectionFactory;

/**
 * Class MassDelete
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\City
 */
class MassDelete extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Stableaddon_RegionalManagement::city_delete';

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
            $model = $this->_objectManager->create(City::class);
            $model->load($id);
            $model->delete();
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', count($ids)));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
