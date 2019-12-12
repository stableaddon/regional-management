<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml;

/**
 * Class Region
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml
 */
abstract class Region extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}
