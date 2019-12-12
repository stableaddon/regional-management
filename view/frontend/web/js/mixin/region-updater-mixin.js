define([
    "jquery"
], function ($) {
    'use strict';

    return function (widget) {

        $.widget('mage.regionUpdater', widget, {

            _updateRegion: function (country) {
                // Clear validation error messages
                var regionList = $(this.options.regionListId),
                    regionInput = $(this.options.regionInputId),
                    postcode = $(this.options.postcodeId),
                    label = regionList.parent().siblings('label'),
                    requiredLabel = regionList.parents('div.field');

                this._clearError();
                this._checkRegionRequired(country);

                // Populate state/province dropdown list if available or use input box
                if (this.options.regionJson[country]) {
                    this._removeSelectOptions(regionList);
                    $.each(this.options.regionJson[country], $.proxy(function (key, value) {
                        this._renderSelectOption(regionList, key, value);
                    }, this));

                    if (this.currentRegionOption) {
                        regionList.val(this.currentRegionOption);
                    }

                    if (this.setOption) {
                        regionList.find('option').filter(function () {
                            return this.text === regionInput.val();
                        }).attr('selected', true);
                    }

                    if (this.options.isRegionRequired) {
                        regionList.addClass('required-entry').removeAttr('disabled');
                        requiredLabel.addClass('required');
                    } else {
                        regionList.removeClass('required-entry validate-select').removeAttr('data-validate');
                        requiredLabel.removeClass('required');

                        if (!this.options.optionalRegionAllowed) {
                            regionList.attr('disabled', 'disabled');
                        }
                    }

                    regionList.show();
                    this._niceSelect('update');
                    regionInput.hide();
                    label.attr('for', regionList.attr('id'));
                } else {
                    if (this.options.isRegionRequired) {
                        regionInput.addClass('required-entry').removeAttr('disabled');
                        requiredLabel.addClass('required');
                    } else {
                        if (!this.options.optionalRegionAllowed) {
                            regionInput.attr('disabled', 'disabled');
                        }
                        requiredLabel.removeClass('required');
                        regionInput.removeClass('required-entry');
                    }

                    regionList.removeClass('required-entry').hide();
                    regionInput.show();
                    label.attr('for', regionInput.attr('id'));
                }

                // If country is in optionalzip list, make postcode input not required
                if (this.options.isZipRequired) {
                    $.inArray(country, this.options.countriesWithOptionalZip) >= 0 ?
                        postcode.removeClass('required-entry').closest('.field').removeClass('required') :
                        postcode.addClass('required-entry').closest('.field').addClass('required');
                }

                // Add defaultvalue attribute to state/province select element
                regionList.attr('defaultvalue', this.options.defaultRegion);


            },

            /**
             * Takes clearError callback function as first option
             * If no form is passed as option, look up the closest form and call clearError method.
             * @private
             */
            _niceSelect: function(elem, type){
                var action = '';
                if(type){
                    action = type;
                }
                if(typeof $.fn.niceSelect !== 'undefined'){
                    elem.niceSelect(action);
                }
            },
            /**
             * Render dropdown list
             * @param {Object} selectElement - jQuery object for dropdown list
             * @param {String} key - region code
             * @param {Object} value - region object
             * @private
             */
            _renderSelectOption: function (selectElement, key, value) {
                selectElement.append($.proxy(function () {
                    var name = value.name.replace(/[!"#$%&'()*+,.\/:;<=>?@[\\\]^`{|}~]/g, '\\$&'),
                        tmplData,
                        tmpl;

                    if (value.code && $(name).is('span')) {
                        key = value.code;
                        value.name = $(name).text();
                    }

                    tmplData = {
                        value: key,
                        title: value.name,


                        isSelected: false
                    };

                    if (this.options.defaultRegion === key) {
                        tmplData.isSelected = true;
                    }

                    tmpl = this.regionTmpl({
                        data: tmplData
                    });

                    return $(tmpl);
                }, this));
            },
        });

        return $.mage.regionUpdater;
    }

});


