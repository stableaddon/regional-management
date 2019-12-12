<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\Region;

use Magento\Backend\App\Action\Context;
use Magento\Directory\Model\Region;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Stableaddon\RegionalManagement\Component\MassAction\Filter;

/**
 * Class Massdelete
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\Region
 */
class Massdelete extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Stableaddon_RegionalManagement::region_delete';

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
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('main_table.region_id', array('in' => $ids));
        $collectionSize = $collection->getSize();

        foreach ($ids as $id) {
            $model = $this->_objectManager->create(Region::class);
            $model->load($id);
            $model->delete();
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
