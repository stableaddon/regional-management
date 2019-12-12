<?php

namespace Stableaddon\RegionalManagement\Observer\Adminhtml;

/**
 * Class OrderBillingToQuote
 *
 * @package Stableaddon\RegionalManagement\Observer\Adminhtml
 */
class OrderBillingToQuote implements \Magento\Framework\Event\ObserverInterface
{
    protected $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    )
    {
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quoteInstance */
        $orderBillingAddress = $observer->getEvent()->getSource();
        $quoteBillingAddress = $observer->getEvent()->getTarget();
        $quoteBillingAddress->setCityId($orderBillingAddress->getCityId())
            ->setSubDistrict($orderBillingAddress->getSubDistrict())
            ->setSubDistrictId($orderBillingAddress->getSubDistrictId());
    }
}