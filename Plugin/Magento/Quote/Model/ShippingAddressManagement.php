<?php

namespace Stableaddon\RegionalManagement\Plugin\Magento\Quote\Model;

/**
 * Class ShippingAddressManagement
 *
 * @package Stableaddon\RegionalManagement\Plugin\Magento\Quote\Model
 */
class ShippingAddressManagement
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
     * ShippingAddressManagement constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Stableaddon\RegionalManagement\Helper\Data $helper
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Stableaddon\RegionalManagement\Helper\Data $helper
    )
    {
        $this->logger = $logger;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Quote\Model\ShippingAddressManagement $subject
     * @param $cartId
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     */
    public function beforeAssign(
        \Magento\Quote\Model\ShippingAddressManagement $subject,
        $cartId,
        \Magento\Quote\Api\Data\AddressInterface $address
    )
    {
        $extAttributes = $address->getExtensionAttributes();
        if (!empty($extAttributes)) {

            foreach ($this->helper->getExtraCheckoutAddressFields('subdistrict_checkout_shipping_address_fields') as $extraField) {
                $set = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $extraField)));
                $get = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $extraField)));

                $value = $extAttributes->$get();
                try {
                    if (isset($value) && $value) {
                        $address->$set($value);
                    }
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage());
                }
            }
        }

    }
}