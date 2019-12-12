<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\City;

use Stableaddon\RegionalManagement\Controller\Adminhtml\Region;

/**
 * Class Index
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\City
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
            'City/District Manager',
            'City/District Manager'
        );
        $resultPage->getConfig()->getTitle()->prepend(__('City/District'));
        $resultPage->getConfig()->getTitle()
            ->prepend('City/District Manager');

        return $resultPage;
    }
}
