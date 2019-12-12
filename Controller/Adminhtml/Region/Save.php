<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\Region;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Directory\Model\Region;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

/**
 * Class Save
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\Region
 */
class Save extends Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Save constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry
    )
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();
        if ($data) {

            $id = $this->getRequest()->getParam('region_id');

            $model = $this->_objectManager->create(Region::class)->load($id);
            if (!$model->getRegionId() && $id) {
                $this->messageManager->addError(__('This region no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved the Province.'));

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['region_id' => $model->getRegionId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the region.'));
            }

            $this->_getSession()->setFormData($data);
            if ($this->getRequest()->getParam('region_id')) {
                return $resultRedirect->setPath('*/*/edit', ['region_id' => $this->getRequest()->getParam('region_id')]);
            }

            return $resultRedirect->setPath('*/*/new');
        }

        return $resultRedirect->setPath('*/*/');
    }
}
