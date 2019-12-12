<?php

namespace Stableaddon\RegionalManagement\Controller\Adminhtml\Region\Name;

/**
 * Class Index
 *
 * @package Stableaddon\RegionalManagement\Controller\Adminhtml\Region\Name
 */
class Index extends \Stableaddon\RegionalManagement\Controller\Adminhtml\Region\Name
{
    /**
     * @return \Stableaddon\RegionalManagement\Controller\Adminhtml\Region\Name\Index
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            'Regions Name Manager',
            'Regions Name Manager'
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Customers'));
        $resultPage->getConfig()->getTitle()
            ->prepend('Regions Name Manager');
        return $resultPage;
    }
}
