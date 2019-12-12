<?php

namespace Stableaddon\RegionalManagement\Plugin\Magento\Sales\Model\AdminOrder;

/**
 * Class Create
 *
 * @package Stableaddon\RegionalManagement\Plugin\Magento\Sales\Model\AdminOrder
 */
class Create
{
    /**
     * @param $subject
     * @param $result
     *
     * @return mixed
     */
    public function afterSetBillingAddress($subject, $result)
    {
        $quote = $result->getQuote();
        $billingAddress = $quote->getBillingAddress();

        $extAttributes = $billingAddress->getExtensionAttributes();

        $extAttributes->setCityId($billingAddress->getData('city_id'));
        $extAttributes->setSubDistrict($billingAddress->getData('sub_district'));
        $extAttributes->setSubDistrictId($billingAddress->getData('sub_district_id'));

        $billingAddress->setExtensionAttributes($extAttributes);

        $quote->setBillingAddress($billingAddress);

        return $result;
    }
}
