<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\Sub\District\Name;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResourceConnection;

/**
 * Class Delete
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\Sub\District\Name
 */
class Delete extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Stableaddon_RegionalManagement::sub_district_name_delete';

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
    const COUNTRY = 'district_id';

    /**
     * @var string
     */
    const TABLE_ENTITY = 'directory_city_district_name';

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
        $this->_connection = $resource->getConnection(ResourceConnectionResourceConnection::DEFAULT_CONNECTION);
        parent::__construct($context);
    }

    public function execute()
    {
        $district_id = $this->getRequest()->getParam('district_id');
        $locale = $this->getRequest()->getParam('locale');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $tableName = $this->_connection->getTableName(self::TABLE_ENTITY);
        if ($district_id && $locale) {
            try {
                $this->_connection->delete($tableName, self::CODE . "='{$locale}' AND " . self::COUNTRY . "='{$district_id}'");
                $this->messageManager->addSuccess(__('The Sub-District has been deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['district_id' => $district_id, 'locale' => $locale]);
            }
        }
        $this->messageManager->addError(__('Sub-District to delete was not found.'));

        return $resultRedirect->setPath('*/*/');
    }
}
