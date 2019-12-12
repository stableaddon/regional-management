<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\Sub\District;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Stableaddon\RegionalManagement\Model\SubDistrict;

/**
 * Class Save
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\Sub\District
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
        Action\Context $context,
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

            $id = $this->getRequest()->getParam('id');

            /** @var \Stableaddon\RegionalManagement\Model\ResourceModel\SubDistrict\ $model */
            $model = $this->_objectManager->create(SubDistrict::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This sub district no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved the sub district.'));

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the sub district.'));
            }

            $this->_getSession()->setFormData($data);
            if ($this->getRequest()->getParam('id')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }

            return $resultRedirect->setPath('*/*/new');
        }

        return $resultRedirect->setPath('*/*/');
    }
}
