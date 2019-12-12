define([
    'jquery',
    'uiRegistry',
    'Magento_Checkout/js/model/quote',
    'mage/utils/wrapper'
], function ($, registry, quote, wrapper) {
    'use strict';

    return function (selectShippingAddressAction) {
        return wrapper.wrap(selectShippingAddressAction, function (originalAction, shippingAddress) {
            var city_id = '';
            var sub_district = '';
            var sub_district_id = '';
            if (typeof shippingAddress['extension_attributes'] === 'undefined') {
                shippingAddress['extension_attributes'] = {};
            }

            if (typeof shippingAddress['extensionAttributes'] === 'undefined') {
                shippingAddress['extensionAttributes'] = {};
            }

            if (typeof shippingAddress.customAttributes !== 'undefined') {
                $.each(shippingAddress.customAttributes , function( key, value ) {
                    if(value.attribute_code === 'sub_district' || value.attribute_code === 'sub_district_id' || value.attribute_code === 'city_id'){
                        if($.isPlainObject(value)){
                            var valueAttribute = value['value'];
                            /**
                             * temp fix select on shipping address lost when edit billing
                             */
                            if (value.attribute_code === 'city_id') {
                                city_id = value['value'];
                            }
                            if (value.attribute_code === 'sub_district_id') {
                                sub_district_id = value['value'];
                            }
                            if (value.attribute_code === 'sub_district') {
                                sub_district = value['value'];
                            }
                        }
                        key = value.attribute_code;
                        shippingAddress['extension_attributes'][key] = valueAttribute;
                        shippingAddress['extensionAttributes'][key] = valueAttribute;   shippingAddress['extension_attributes'][key] = valueAttribute;
                        shippingAddress['extensionAttributes'][key] = valueAttribute;
                    }
                });
            }

            originalAction(shippingAddress);
        });
    };
});
