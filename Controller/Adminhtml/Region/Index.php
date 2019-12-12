<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\Region;

use Stableaddon\RegionalManagement\Controller\Adminhtml\Region;

/**
 * Class Index
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\Region
 */
class Index extends Region
{
    /**
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            'Regions Manager',
            'Regions Manager'
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Customers'));
        $resultPage->getConfig()->getTitle()
            ->prepend('Regions Manager');

        return $resultPage;
    }
}
