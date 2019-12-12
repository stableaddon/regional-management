<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\Sub\District\Name;

use Stableaddon\RegionalManagement\Controller\Adminhtml\Region\Name;

/**
 * Class Index
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\Sub\District\Name
 */
class Index extends Name
{
    /**
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            'Sub District Name Manager',
            'Sub District Name Manager'
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Customers'));
        $resultPage->getConfig()->getTitle()
            ->prepend('Sub Districts Name Manager');
        return $resultPage;
    }
}
