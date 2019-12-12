<?php

namespace Stableaddon\RegionalManagement\Controller\Search;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class City
 *
 * @package Stableaddon\RegionalManagement\Controller\Search
 */
class City extends \Magento\Framework\App\Action\Action
{
    /**
     * @var null
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Stableaddon\RegionalManagement\Helper\Data
     */
    protected $_helper;

    /**
     * City constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Stableaddon\RegionalManagement\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Stableaddon\RegionalManagement\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_helper = $helper;
    }
    public function execute() {
        $collection = $this->_helper->getCityCollection();
        $city = [];
        $k = 0;
        foreach ($collection as $item) {
            if($item->getRegionId()==$this->getRequest()->getParam('region')){
                $city[$k] = [
                    'code' => $item->getId(),
                    'name' => (string)__($item->getName()),
                ];
                $k++;
            }

        }

        $result = $this->resultJsonFactory->create();

        /** You may introduce your own constants for this custom REST API */
        $result->setData(['data' => $city]);
        return $result;
    }
}