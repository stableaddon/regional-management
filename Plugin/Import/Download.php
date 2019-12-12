<?php

namespace Stableaddon\RegionalManagement\Plugin\Import;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Download
 *
 * @package Stableaddon\RegionalManagement\Plugin\Import
 */
class Download
{
    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    private $reader;
    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    private $readFactory;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;
    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    private $resultRedirectFactory;
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    private $fileFactory;
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    private $resultRawFactory;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $requestInterface;

    /**
     * Download constructor.
     *
     * @param \Magento\Framework\App\RequestInterface $requestInterface
     * @param \Magento\Framework\Module\Dir\Reader $reader
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $requestInterface,
        \Magento\Framework\Module\Dir\Reader $reader,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {

        $this->reader = $reader;
        $this->readFactory = $readFactory;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->fileFactory = $fileFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->requestInterface = $requestInterface;
    }

    /**
     * @param $subject
     * @param $result
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\Result\Raw
     */
    public function afterExecute(
        $subject,
        $result
    ) {
        if (in_array($this->requestInterface->getParam('filename'), [
            'directory_country_region',
            'directory_country_region_name',
            'directory_region_city',
            'directory_region_city_name',
            'directory_city_district',
            'directory_city_district_name'
        ])) {
            $fileName = $this->requestInterface->getParam('filename') . '.csv';
            $moduleDir = $this->reader->getModuleDir('', 'Stableaddon_RegionalManagement');;
            $fileAbsolutePath = $moduleDir . '/Files/Sample/' . $fileName;
            $directoryRead = $this->readFactory->create($moduleDir);
            $filePath = $directoryRead->getRelativePath($fileAbsolutePath);

            if (!$directoryRead->isFile($filePath)) {
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $this->messageManager->addError(__('There is no sample file for this entity.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/import');
                return $resultRedirect;
            } else {
                $this->messageManager->getMessages(true);
            }

            $fileSize = isset($directoryRead->stat($filePath)['size'])
                ? $directoryRead->stat($filePath)['size'] : null;

            $this->fileFactory->create(
                $fileName,
                null,
                DirectoryList::VAR_DIR,
                'application/octet-stream',
                $fileSize
            );

            /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
            $resultRaw = $this->resultRawFactory->create();
            $resultRaw->setContents($directoryRead->readFile($filePath));
            return $resultRaw;
        }

        return $result;
    }
}