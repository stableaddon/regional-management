<?php

namespace Stableaddon\RegionalManagement\Observer\Adminhtml;

/**
 * Class OrderShippingToQuote
 *
 * @package Stableaddon\RegionalManagement\Observer\Adminhtml
 */
class OrderShippingToQuote implements \Magento\Framework\Event\ObserverInterface
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
        $orderShippingAddress = $observer->getEvent()->getSource();
        $quoteShippingAddress = $observer->getEvent()->getTarget();
        $quoteShippingAddress->setCityId($orderShippingAddress->getCityId())
            ->setSubDistrict($orderShippingAddress->getSubDistrict())
            ->setSubDistrictId($orderShippingAddress->getSubDistrictId());
    }
}