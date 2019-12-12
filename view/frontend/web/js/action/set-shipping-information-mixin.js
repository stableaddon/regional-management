/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, quote) {
    'use strict';

    return function (setShippingInformationAction) {

        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var shippingAddress = quote.shippingAddress();
            if (shippingAddress['extension_attributes'] === undefined) {
                shippingAddress['extension_attributes'] = {};
            }
            if (typeof shippingAddress.customAttributes != 'undefined' && shippingAddress.customAttributes) {
                if (typeof shippingAddress.customAttributes['city_id'] != 'undefined') {
                    shippingAddress['extension_attributes']['city_id'] = shippingAddress.customAttributes['city_id'].value;
                }
                if (typeof shippingAddress.customAttributes['sub_district_id'] != 'undefined') {
                    shippingAddress['extension_attributes']['sub_district_id'] = shippingAddress.customAttributes['sub_district_id'].value;
                }
                if (typeof shippingAddress.customAttributes['sub_district'] != 'undefined') {
                    shippingAddress['extension_attributes']['sub_district'] = shippingAddress.customAttributes['sub_district'].value;
                }
            }

            return originalAction();
        });
    };
});