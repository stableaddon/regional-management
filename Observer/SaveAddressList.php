<?php

namespace Stableaddon\RegionalManagement\Observer;

/**
 * Class SaveAddressList
 *
 * @package Stableaddon\RegionalManagement\Observer
 */
class SaveAddressList implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * SaveAddressList constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
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
        $quoteInstance = $observer->getEvent()->getQuote();
        $quoteShippingAddress = $quoteInstance->getShippingAddress();
        $quoteBillingAddress = $quoteInstance->getBillingAddress();

        $connection = $this->resource->getConnection();
        $table = $this->resource->getTableName('customer_address_entity');
        try {
            if ($quoteShippingAddress->getData('save_in_address_book') == 1
                && $quoteShippingAddress->getData('city_id')
                && $quoteShippingAddress->getData('sub_district_id')) {
                $data = ['city_id' => $quoteShippingAddress->getData('city_id'),
                    'sub_district' => $quoteShippingAddress->getData('sub_district'),
                    'sub_district_id' => $quoteShippingAddress->getData('sub_district_id')];
                $where = ['entity_id = ?' => $quoteShippingAddress->getData('customer_address_id')];
                $connection->update($table, $data, $where);
            }

            if ($quoteBillingAddress->getData('save_in_address_book') == 1
                && $quoteShippingAddress->getData('city_id')
                && $quoteShippingAddress->getData('sub_district_id')) {
                $data = ['city_id' => $quoteBillingAddress->getData('city_id'),
                    'sub_district_id' => $quoteBillingAddress->getData('sub_district_id'),
                    'sub_district' => $quoteBillingAddress->getData('sub_district')];
                $where = ['entity_id = ?' => $quoteBillingAddress->getData('customer_address_id')];
                $connection->update($table, $data, $where);
            }
        } catch (\Exception $e) {

        }
    }
}