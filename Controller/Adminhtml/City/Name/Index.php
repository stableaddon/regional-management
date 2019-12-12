<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\City\Name;

/**
 * Class Index
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\City\Name
 */
class Index extends \Stableaddon\RegionalManagement\Controller\Adminhtml\Region\Name
{
    /**
     * @return \Stableaddon\RegionalManagement\Controller\Adminhtml\City\Name\Index
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            'City/District Name Manager',
            'City/District Name Manager'
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Customers'));
        $resultPage->getConfig()->getTitle()
            ->prepend('City/Districts Name Manager');
        return $resultPage;
    }
}
