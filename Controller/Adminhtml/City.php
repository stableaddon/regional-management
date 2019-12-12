<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class City
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml
 */
abstract class City extends Action
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
