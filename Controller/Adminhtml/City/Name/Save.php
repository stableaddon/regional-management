<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\City\Name;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

class Save extends \Magento\Backend\App\Action
{
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
    const TABLE_ENTITY = 'directory_region_city_name';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_connection;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * Save constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        ResourceConnection $resource,
        Registry $coreRegistry
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_resource = $resource;
        $this->_connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
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
            $id = $this->getRequest()->getParam('city_id');
            $tableName = $this->_connection->getTableName(self::TABLE_ENTITY);

            try {
                $sql = $this->_connection->select()->from($tableName, self::NAME)
                    ->where(self::CODE . '=?', $data['locale'])
                    ->where(self::COUNTRY . '=?', $id);
                if ($this->_connection->fetchOne($sql)) {
                    $this->_connection->update($tableName, [self::NAME => $data['name']], self::CODE . "='{$data['locale']}' AND " . self::COUNTRY . "='{$id}'");
                } else {
                    $this->_connection->insert($tableName, [self::NAME => $data['name'], self::CODE => $data['locale'], self::COUNTRY => $id]);
                }
                $this->messageManager->addSuccess(__('You saved the district.'));

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', [
                        'city_id' => $this->getRequest()->getParam('city_id'),
                        'locale' => $this->getRequest()->getParam('locale')
                    ]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addException($e, __($e->getMessage() . 'Something went wrong while saving the district.'));
            }

            $this->_getSession()->setFormData($data);
            if ($this->getRequest()->getParam('city_id')) {
                return $resultRedirect->setPath('*/*/edit', [
                    'city_id' => $this->getRequest()->getParam('city_id'),
                    'locale' => $this->getRequest()->getParam('locale')
                ]);
            }

            return $resultRedirect->setPath('*/*/new');
        }

        return $resultRedirect->setPath('*/*/');
    }
}
