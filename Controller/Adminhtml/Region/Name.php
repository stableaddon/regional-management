<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\Region;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Name
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\Region
 */
abstract class Name extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
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
