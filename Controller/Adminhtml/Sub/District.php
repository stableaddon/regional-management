<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\Sub;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class District
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\Sub
 */
abstract class District extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * District constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory  = $resultPageFactory;
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
