<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\City\Name;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResourceConnection;

/**
 * Class Delete
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\City\Name
 */
class Delete extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Stableaddon_RegionalManagement::city_name_delete';

    /**
     * @var string
     */
    const NAME = 'name';

    /**
     * @var string
     */
    const CODE = 'locale';

    /**
     * @var string
     */
    const COUNTRY = 'city_id';

    /**
     * @var string
     */
    const TABLE_Entity = 'directory_region_city_name';

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_connection;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * Delete constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        ResourceConnection $resource
    ) {
        $this->_resource = $resource;
        $this->_connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        parent::__construct($context);
    }

    public function execute()
    {
        $city_id = $this->getRequest()->getParam('city_id');
        $locale = $this->getRequest()->getParam('locale');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $tableName = $this->_connection->getTableName(self::TABLE_Entity);
        if ($city_id && $locale) {
            try {
                $this->_connection->delete($tableName, self::CODE."='{$locale}' AND ". self::COUNTRY ."='{$city_id}'");
                $this->messageManager->addSuccess(__('The City/District has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['city_id' => $city_id, 'locale' => $locale]);
            }
        }
        $this->messageManager->addError(__('City/District to delete was not found.'));

        return $resultRedirect->setPath('*/*/');
    }
}
