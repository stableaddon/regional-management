/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract'
], function (_, registry, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            imports: {
                update: '${ $.parentName }.sub_district_id:value'
            }
        },

        /**
         * @param {String} value
         */
        update: function (value) {
            registry.get(this.parentName + '.' + 'postcode');
            if(window.postcodeJson[value]){
                this.validation['required-entry'] = true;
                this.required(true);
                this.visible(true);
                registry.get(this.parentName + '.' + 'postcode').value(window.postcodeJson[value]);
            }else{
                registry.get(this.parentName + '.' + 'postcode').value('');
            }
        }
    });
});