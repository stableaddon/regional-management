define([
    'jquery',
    'uiRegistry',
    'Magento_Checkout/js/model/quote',
    'mage/utils/wrapper'
], function ($, registry, quote, wrapper) {
    'use strict';

    return function (selectShippingAddressAction) {
        return wrapper.wrap(selectShippingAddressAction, function (originalAction, billingAddress) {
            if (typeof billingAddress['extension_attributes'] === 'undefined') {
                billingAddress['extension_attributes'] = {};
            }

            if (typeof billingAddress['extensionAttributes'] === 'undefined') {
                billingAddress['extensionAttributes'] = {};
            }

            if (typeof billingAddress.customAttributes !== 'undefined') {
                $.each(billingAddress.customAttributes , function( key, value ) {
                    if(value.attribute_code === 'sub_district' || value.attribute_code === 'sub_district_id' || value.attribute_code === 'city_id'){
                        if($.isPlainObject(value)){
                            var valueAttribute = value['value'];
                        }
                        key = value.attribute_code;
                        billingAddress['extensionAttributes'][key] = valueAttribute
                        billingAddress['extension_attributes'][key] = valueAttribute
                    }
                });
            }
            originalAction(billingAddress);
        });
    };
});
