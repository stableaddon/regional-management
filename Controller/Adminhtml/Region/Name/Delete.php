<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\Region\Name;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResourceConnection;

/**
 * Class Delete
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\Region\Name
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
    const COUNTRY = 'region_id';

    /**
     * @var string
     */
    const TABLE_Entity = 'directory_country_region_name';

    /**
     * @var ResourceConnection
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
     */
    public function __construct(
        Context $context,
        ResourceConnection $resource
    )
    {
        $this->_resource = $resource;
        $this->_connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $region_id = $this->getRequest()->getParam('region_id');
        $locale = $this->getRequest()->getParam('locale');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $tableName = $this->_connection->getTableName(self::TABLE_Entity);
        if ($region_id && $locale) {
            try {
                $this->_connection->delete($tableName, self::CODE . "='{$locale}' AND " . self::COUNTRY . "='{$region_id}'");
                $this->messageManager->addSuccess(__('The Region has been deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['region_id' => $region_id, 'locale' => $locale]);
            }
        }
        $this->messageManager->addError(__('Region to delete was not found.'));

        return $resultRedirect->setPath('*/*/');
    }
}
