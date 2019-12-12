<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\Sub\District;

use Stableaddon\RegionalManagement\Controller\Adminhtml\Sub\District;

/**
 * Class Index
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\Sub\District
 */
class Index extends District
{
    /**
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            'Sub District Manager',
            'Sub District Manager'
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Customers'));
        $resultPage->getConfig()->getTitle()
            ->prepend('Sub District Manager');

        return $resultPage;
    }
}
