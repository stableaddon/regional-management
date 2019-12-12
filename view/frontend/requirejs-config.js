var config = {
    map: {
        '*': {
            'regionsManager' : 'Stableaddon_RegionalManagement/js/regions-manager',
            'Magento_Checkout/template/billing-address/details.html' :  'Stableaddon_RegionalManagement/template/billing-address/details.html',
            'Magento_Checkout/template/shipping-address/address-renderer/default.html' :  'Stableaddon_RegionalManagement/template/shipping-address/address-renderer/default.html',
            'Magento_Checkout/template/shipping-information/address-renderer/default.html' :  'Stableaddon_RegionalManagement/template/shipping-information/address-renderer/default.html',
            'Magento_Checkout/js/view/shipping-address/address-renderer/default' : 'Stableaddon_RegionalManagement/js/view/shipping-address/address-renderer/default-mixin'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information' : {
                'Stableaddon_RegionalManagement/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information' : {
                'Stableaddon_RegionalManagement/js/action/set-payment-information-mixin': true
            },
            'Magento_Checkout/js/action/select-shipping-address' : {
                'Stableaddon_RegionalManagement/js/action/select-shipping-address-mixin': true
            },
            'Magento_Checkout/js/action/select-billing-address' : {
                'Stableaddon_RegionalManagement/js/action/select-billing-address-mixin': true
            },
            'Magento_Checkout/js/region-updater': {
                'Stableaddon_RegionalManagement/js/mixin/region-updater-mixin' : true
            }
        }
    }
};