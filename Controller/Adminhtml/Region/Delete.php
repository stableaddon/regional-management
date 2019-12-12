<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\Region;

/**
 * Class Delete
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\Region
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Stableaddon_RegionalManagement::region_delete';

    /**
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $region_id = $this->getRequest()->getParam('region_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($region_id) {
            try {
                $model = $this->_objectManager->create('Magento\Directory\Model\Region');
                $model->load($region_id);
                $regionName = $model->getDefaultName();
                $model->delete();
                $this->messageManager->addSuccess(__('The %1 Region has been deleted.', $regionName));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['region_id' => $region_id]);
            }
        }
        $this->messageManager->addError(__('Region to delete was not found.'));

        return $resultRedirect->setPath('*/*/');
    }
}
