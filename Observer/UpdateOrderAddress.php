<?php

namespace Stableaddon\RegionalManagement\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class UpdateOrderAddress
 *
 * @package Stableaddon\RegionalManagement\Observer
 */
class UpdateOrderAddress implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Sales\Api\OrderAddressRepositoryInterface
     */
    protected $orderAddressRepository;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Stableaddon\RegionalManagement\Helper\Data
     */
    protected $helper;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Sales\Api\OrderAddressRepositoryInterface $orderAddressRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Psr\Log\LoggerInterface $logger,
        \Stableaddon\RegionalManagement\Helper\Data $helper
    )
    {
        $this->resource = $resource;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
        $this->helper = $helper;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    )
    {
        /** @var \Magento\Sales\Model\Order $orderInstance */
        $orderInstance = $observer->getEvent()->getOrder();
        /** @var \Magento\Quote\Model\Quote $quoteInstance */
        $quoteInstance = $observer->getEvent()->getQuote();

        foreach ($this->helper->getExtraCheckoutAddressFields('subdistrict_checkout_billing_address_fields') as $extraField) {
            $set = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $extraField)));
            $get = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $extraField)));

            try {
                $orderInstance->getBillingAddress()->$set($quoteInstance->getBillingAddress()->$get())->save();
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }

        foreach ($this->helper->getExtraCheckoutAddressFields('subdistrict_checkout_shipping_address_fields') as $extraField) {
            $set = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $extraField)));
            $get = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $extraField)));

            try {
                $orderInstance->getShippingAddress()->$set($quoteInstance->getShippingAddress()->$get())->save();
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }

        $customerAddressTable = $this->resource->getTableName('customer_address_entity');
        $connection = $this->resource->getConnection();
        $quoteAddressTable = $this->resource->getTableName('quote_address');
        $select = $connection->select()->from(['qa' => $quoteAddressTable], ['quote_id', 'address_type', 'city_id', 'sub_district', 'sub_district_id', 'customer_address_id', 'save_in_address_book'])->where('quote_id = ?', $quoteInstance->getId());
        $addrs = $connection->fetchAll($select);

        foreach ($addrs as $addr) {
            $data[$addr['address_type']] = [
                'quote_id' => $addr['quote_id'],
                'city_id' => $addr['city_id'],
                'sub_district' => $addr['sub_district'],
                'sub_district_id' => $addr['sub_district_id'],
                'customer_address_id' => $addr['customer_address_id'],
                'save_in_address_book' => $addr['save_in_address_book']
            ];
        }

        $orderShippingAddress = $orderInstance->getShippingAddress();
        $orderBillingAddress = $orderInstance->getBillingAddress();
        if ($orderShippingAddress !== null) {
            $orderShippingAddress->setData('city_id', $data['shipping']['city_id']);
            $orderShippingAddress->setData('sub_district', $data['shipping']['sub_district']);
            $orderShippingAddress->setData('sub_district_id', $data['shipping']['sub_district_id']);
            if ($customerAddressId = $data['shipping']['customer_address_id']
                && $data['shipping']['city_id']
                && $data['shipping']['sub_district_id']) {
                $dataUpdate = ['city_id' => $data['shipping']['city_id'],
                    'sub_district_id' => $data['shipping']['sub_district_id']];
                $where = ['entity_id = ?' => $customerAddressId];
                $connection->update($customerAddressTable, $dataUpdate, $where);
            } else {
                if ($data['shipping']['save_in_address_book']) {
                    if ($customerId = $connection->fetchOne('select customer_id from ' . $quoteAddressTable . ' where quote_id=?', [$data['shipping']['quote_id']])) {
                        $dataUpdate = ['city_id' => $data['shipping']['city_id'],
                            'sub_district_id' => $data['shipping']['sub_district_id'],
                            'sub_district' => $data['shipping']['sub_district']];
                        $where = ['entity_id = ?' => $customerAddressId];
                        $connection->update($customerAddressTable, $dataUpdate, $where);
                    }
                }
            }
        }

        if ($orderBillingAddress !== null) {
            $orderBillingAddress->setData('city_id', $data['billing']['city_id']);
            $orderBillingAddress->setData('sub_district', $data['billing']['sub_district']);
            $orderBillingAddress->setData('sub_district_id', $data['billing']['sub_district_id']);
            if ($customerAddressId = $data['billing']['customer_address_id']
                && $data['billing']['city_id']
                && $data['billing']['sub_district_id']) {
                $dataUpdate = ['city_id' => $data['billing']['city_id'],
                    'sub_district_id' => $data['billing']['sub_district_id']];
                $where = ['entity_id = ?' => $customerAddressId];
                $connection->update($customerAddressTable, $dataUpdate, $where);
            } else {
                if ($data['billing']['save_in_address_book']) {
                    if ($customerId = $connection->fetchOne('select customer_address_id from ' . $quoteAddressTable . ' where quote_id=?', [$data['billing']['quote_id']])) {
                        $dataUpdate = ['city_id' => $data['billing']['city_id'],
                            'sub_district_id' => $data['billing']['sub_district_id'],
                            'sub_district' => $data['billing']['sub_district']];
                        $where = ['entity_id = ?' => $customerAddressId];
                        $connection->update($customerAddressTable, $dataUpdate, $where);
                    }
                }
            }
        }
    }
}
