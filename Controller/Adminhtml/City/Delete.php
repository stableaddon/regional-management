<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\City;

use Stableaddon\RegionalManagement\Model\City;

/**
 * Class Delete
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\City
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Stableaddon_RegionalManagement::city_delete';

    /**
     * @return mixed
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->_objectManager->create(City::class);
                $model->load($id);
                $regionName = $model->getName();
                $model->delete();
                $this->messageManager->addSuccess(__('The %1 city/district has been deleted.', $regionName));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('City/district to delete was not found.'));

        return $resultRedirect->setPath('*/*/');
    }
}
