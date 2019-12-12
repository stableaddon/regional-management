<?php

namespace Stableaddon\RegionalManagement\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * Class UpdateCustomerAddress
 *
 * @package Stableaddon\RegionalManagement\Observer
 */
class UpdateCustomerAddress implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * UpdateCustomerAddress constructor.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Registry $registry
    ) {
        $this->request = $request;
        $this->resource = $resource;
        $this->_registry = $registry;
    }

    /**
     * Update address
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $customerAddress = $observer->getCustomerAddress();
        $params = $this->request->getParams();
        if(!array_key_exists('sub_district_id', $params) || !$params['sub_district_id']){
            $params['sub_district_id'] = 0;
        }

        if (array_key_exists('city_id', $params) && array_key_exists('sub_district_id', $params)) {
            $connection = $this->resource->getConnection();
            $table = $this->resource->getTableName('customer_address_entity');
            $data = ['city_id' => $params['city_id'],
                'sub_district_id' => $params['sub_district_id'],
                'sub_district' => $params['sub_district']];
            $where = ['entity_id = ?' => $customerAddress->getId()];
            $connection->update($table, $data, $where);
        }
        $addressId = $customerAddress->getId();
        $number_address_new = $this->_registry->registry('customer_new_address_number') ?
            $this->_registry->registry('customer_new_address_number') : 0;

        if (isset($params['address'][$addressId])) {
            $addressData = $params['address'][$addressId];
            if(!array_key_exists('sub_district_id', $addressData) || !$addressData['sub_district_id']){
                $addressData['sub_district_id'] = 0;
            }
            if (array_key_exists('city_id', $addressData) &&
                array_key_exists('sub_district_id', $addressData)
                && $addressData['city_id'] && $addressData['sub_district_id']) {
                $connection = $this->resource->getConnection();
                $table = $this->resource->getTableName('customer_address_entity');
                $data = ['city_id' => $addressData['city_id'],
                    'sub_district_id' => $addressData['sub_district_id'],
                    'sub_district' => $addressData['sub_district']];
                $where = ['entity_id = ?' => $addressId];
                $connection->update($table, $data, $where);
            }
        } elseif ($addressId
            && isset($params['address']['new_'.$number_address_new])
            && !$customerAddress->getOrigData()) {
            $addressData = $params['address']['new_'.$number_address_new];
            if(!array_key_exists('sub_district_id', $addressData) || !$addressData['sub_district_id']){
                $addressData['sub_district_id'] = 0;
            }
            if (array_key_exists('city_id', $addressData)
                && array_key_exists('sub_district_id', $addressData)
                && $addressData['city_id'] && $addressData['sub_district_id']) {
                $connection = $this->resource->getConnection();
                $table = $this->resource->getTableName('customer_address_entity');
                $data = ['city_id' => $addressData['city_id'],
                    'sub_district_id' => $addressData['sub_district_id'],
                    'sub_district' => $addressData['sub_district']];
                $where = ['entity_id = ?' => $customerAddress->getId()];
                $connection->update($table, $data, $where);
            }
            $this->_registry->unregister('customer_new_address_number');

            $this->_registry->register('customer_new_address_number',$number_address_new + 1);
        }
    }
}