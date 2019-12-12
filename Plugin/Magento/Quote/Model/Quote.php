<?php

namespace Stableaddon\RegionalManagement\Plugin\Magento\Quote\Model;

/**
 * Class Quote
 *
 * @package Stableaddon\RegionalManagement\Plugin\Magento\Quote\Model
 */
class Quote
{
    /**
     * @var \Stableaddon\RegionalManagement\Helper\Data
     */
    protected $helper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Customer\Model\Address
     */
    protected $address;

    /**
     * Quote constructor.
     *
     * @param \Stableaddon\RegionalManagement\Helper\Data $helper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Customer\Model\Address $address
     */
    public function __construct(
        \Stableaddon\RegionalManagement\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Model\Address $address
    )
    {
        $this->logger = $logger;
        $this->helper = $helper;
        $this->address = $address;
    }

    /**
     * @param \Magento\Quote\Model\Quote $subject
     * @param \Magento\Quote\Api\Data\AddressInterface|null $address
     *
     * @return array
     */
    public function beforeSetBillingAddress(
        \Magento\Quote\Model\Quote $subject,
        \Magento\Quote\Api\Data\AddressInterface $address = null
    ) {
        $extAttributes = $address->getExtensionAttributes();
        $addressId = $address->getCustomerAddressId();
        if($addressId){
            $dataAddress = $this->address->load($addressId);
        }
        if (!empty($extAttributes)) {

            foreach($this->helper->getExtraCheckoutAddressFields('subdistrict_checkout_billing_address_fields') as $extraField) {

                $set = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $extraField)));
                $get = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $extraField)));

                $value = $extAttributes->$get();
                try {
                    if(isset($value) && $value){
                        $address->$set($value);
                    }else{
                        if ($addressId) {
                            $address->$set($dataAddress->$get());
                        }
                    }
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage());
                }
            }
        }
        return [$address];
    }

    /**
     * @param \Magento\Quote\Model\Quote $subject
     * @param \Magento\Quote\Api\Data\AddressInterface|null $address
     *
     * @return array
     */
    public function beforeSetShippingAddress(
        \Magento\Quote\Model\Quote $subject,
        \Magento\Quote\Api\Data\AddressInterface $address = null
    ) {
        $addressId = $address->getCustomerAddressId();
        if($addressId){
            $dataAddress = $this->address->load($addressId);
        }
        $extAttributes = $address->getExtensionAttributes();
        if (!empty($extAttributes)) {

            foreach($this->helper->getExtraCheckoutAddressFields('subdistrict_checkout_billing_address_fields') as $extraField) {
                $set = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $extraField)));
                $get = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $extraField)));

                $value = $extAttributes->$get();

                try {
                    if(isset($value) && $value){
                        $address->$set($value);
                    }else{
                        if (isset($dataAddress)) {
                            $address->$set($dataAddress->$get());
                        }
                    }
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage());
                }
            }
        }
        return [$address];
    }
}