/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, quote) {
    'use strict';

    return function (setPaymentInformationAction) {

        return wrapper.wrap(setPaymentInformationAction, function (originalAction, messageContainer, paymentData) {
            var billingAddress = quote.billingAddress();
            if (billingAddress['extension_attributes'] === undefined) {
                billingAddress['extension_attributes'] = {};
            }
            if (typeof billingAddress.customAttributes != 'undefined' && billingAddress.customAttributes) {
                if (typeof billingAddress.customAttributes['city_id'] != 'undefined') {
                    billingAddress['extension_attributes']['city_id'] = billingAddress.customAttributes['city_id'].value;
                }
                if (typeof billingAddress.customAttributes['sub_district_id'] != 'undefined') {
                    billingAddress['extension_attributes']['sub_district_id'] = billingAddress.customAttributes['sub_district_id'].value;
                }
                if (typeof billingAddress.customAttributes['sub_district'] != 'undefined') {
                    billingAddress['extension_attributes']['sub_district'] = billingAddress.customAttributes['sub_district'].value;
                }
            }

            return originalAction(messageContainer, paymentData);
        });
    };
});