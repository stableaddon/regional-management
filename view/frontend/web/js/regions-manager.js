/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/template',
    'underscore',
    'jquery/ui',
    'mage/validation',
    'domReady!'
], function ($, mageTemplate, _, ui, Validation, url) {
    'use strict';

    $.widget('mage.regionsManager', {
        options: {
            optionTemplate:
                '<option value="<%- data.value %>" <% if (data.isSelected) { %>selected="selected"<% } %>>' +
                    '<%- data.title %>' +
                '</option>',
            currentCity: null,
            currentDistrict: null,
            currentRegion: null,
            currentDistrictJson: null
        },

        /**
         *
         * @private
         */
        _create: function () {
            var self = this;
            this.cityElem = $(this.options.cityInputId);
            this.cityList = $(this.options.cityListId);
            this.districtElem = $(this.options.districtInputId);
            this.districtList = $(this.options.districtListId);
            this.regionList = $(this.options.regionListId);
            this.countryList = $('#country');
            this.postcodeInput = $(this.options.zipInputId);
            this.defaultRegion = $(this.options.defaultRegion);
            this.defaultCityId = $(this.options.defaultCityId);
            this.defaultSubDistrictId = $(this.options.defaultSubDistrictId);
            this._initCityElement();
            this._initDistrictElement();
            this.currentCityOption = this.options.currentCity;
            this.optionTmpl = mageTemplate(this.options.optionTemplate);
            this.cityLabel = this.cityList.parents('div.field');
            this.districtLabel = this.districtList.parents('div.field');

            self._updateCity(self.defaultRegion.selector);
            self._updateDistrict(self.defaultCityId.selector);

        },

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
                regionList.niceSelect('update');
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
                regionList.niceSelect('destroy');
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
         *
         * @private
         */
        _initCityElement: function () {
            this._toggleCityInput();
            this.regionList.on('change', $.proxy(function (e) {
                this._updateCity($(e.target).val());
            }, this));

            this.countryList.on('change', $.proxy(function (e) {
                this._updateCity($(e.target).val());
            }, this));
            this.cityList.on('change', $.proxy(function (e) {
                this.cityElem.val($(e.target).find('option:selected').text());
            }, this));
        },
        _toggleCityInput: function(status){
            if(status && status === true){
                this.cityElem.show();
            }
            this.cityElem.hide();
        },
        _initDistrictElement: function(){
            this._toggleDistrictInput();
            this.cityList.on('change', $.proxy(function (e) {
                this._updateDistrict($(e.target).val());
            }, this));
            this.districtList.on('change', $.proxy(function(e){
                this.districtElem.val($(e.target).find('option:selected').text());
                this._updatePostcode($(e.target).val());
            }, this));
        },
        _toggleDistrictInput: function(status){
            if(status && status === true){
                this.districtElem.show();
            }
            this.districtElem.hide();
        },
        /**
         * Remove options from dropdown list
         *
         * @param {Object} selectElement - jQuery object for dropdown list
         * @private
         */
        _removeSelectOptions: function (selectElement) {
            selectElement.find('option').each(function (index) {
                if (index) {
                    $(this).remove();
                }
            });
        },

        /**
         * Render dropdown list
         * @param {string} type of options
         * @param {Object} selectElement - jQuery object for dropdown list
         * @param {String} key - region code
         * @param {Object} value - region object
         * @private
         */
        _renderSelectOption: function (type, selectElement, key, value) {
            var isSelected = false;
            if(type==='city'){
                isSelected = value.code===this.defaultCityId.selector;
            }

            if(type==='sub-district'){
                isSelected = value.code===this.defaultSubDistrictId.selector;
            }
            
            var self = this;
            selectElement.append(function () {
                var name = value.name.replace(/[!"#$%&'()*+,.\/:;<=>?@[\\\]^`{|}~]/g, '\\$&'),
                    tmplData,
                    tmpl;

                if (value.code && $(name).is('span')) {
                    key = value.code;
                    value.name = $(name).text();
                }

                tmplData = {
                    value: value.code,
                    title: value.name,
                    isSelected: isSelected
                };

                tmpl = self.optionTmpl({
                    data: tmplData
                });

                return $(tmpl);
            });
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
        _clearError: function () {
            var args = ['clearError', this.options.regionListId, this.options.regionInputId, this.options.postcodeId];

            if (this.options.clearError && typeof this.options.clearError === 'function') {
                this.options.clearError.call(this);
            } else {
                if (!this.options.form) {
                    this.options.form = this.element.closest('form').length ? $(this.element.closest('form')[0]) : null;
                }

                this.options.form = $(this.options.form);

                this.options.form && this.options.form.data('validator') &&
                    this.options.form.validation.apply(this.options.form, _.compact(args));

                // Clean up errors on region & zip fix
                $(this.options.regionInputId).removeClass('mage-error').parent().find('[generated]').remove();
                $(this.options.regionListId).removeClass('mage-error').parent().find('[generated]').remove();
                $(this.options.postcodeId).removeClass('mage-error').parent().find('[generated]').remove();
            }
        },

        _updateCity: function (region) {
            // Clear validation error messages
            var self = this;
            if(region === ''){
                self.cityList.removeClass('required-entry').prop('disabled', 'disabled').hide();
                self.cityElem.show();
                self.cityLabel.removeClass('required');
                self._updateDistrict(this.cityList.val());
                return;
            }

            this._clearError();
            $.ajax({
                url: self.options.citySearchUrl,
                type: 'post',
                dataType: 'json',
                data: {
                    region: region
                },
                showLoader: true,
                beforeSend: function () {
                    $("#city_id option").each(function(){
                        if($(this).val()!=''){
                            $(this).remove();
                        }
                    });
                    self.cityList.prop('disable', true);
                    self._niceSelect(self.cityList, 'update');
                },
                success: function (response) {
                    if(response.data !== {} && response.data.length > 0){
                        $.each(response.data, function (key, value) {
                            self._renderSelectOption('city', self.cityList, key, value);
                        });
                        self.cityList.addClass('required-entry').removeAttr('disabled');
                        self.cityList.show();
                        self.cityElem.hide();
                        self.cityLabel.addClass('required');
                    }else{
                        self.cityList.removeClass('required-entry').prop('disabled', 'disabled').hide();
                        self.cityElem.show();
                        self._updateDistrict(self.cityList.val());
                        self.cityLabel.removeClass('required');
                    }
                    self.cityList.removeAttr('disable');
                    self._niceSelect(self.cityList, 'update');
                }
            });
        },

        _updateDistrict: function (city) {
            // Clear validation error messages
            var self = this;
            if(city === ''){
                // self.districtList.removeClass('required-entry').prop('disabled', 'disabled').hide();
                self.districtList.removeClass('required-entry').hide();
                self.districtElem.show();
                self.districtLabel.removeClass('required');
                return;
            }
            this._clearError();
            $.ajax({
                url: self.options.districtSearchUrl,
                type: 'post',
                dataType: 'json',
                data: {
                    city: city
                },
                showLoader: true,
                beforeSend: function () {
                    $("#sub_district_id option").each(function(){
                        if($(this).val()!=''){
                            $(this).remove();
                        }
                    });
                    self.districtList.prop('disable', true);
                    self._niceSelect(self.districtList, 'update');
                },
                success: function (response) {
                    if(response.data !== {} && response.data.length > 0){
                        self.options.currentDistrictJson = response.data;
                        $.each(response.data, function (key, value) {
                            self._renderSelectOption('sub-district', self.districtList, key, value);
                        });
                        self.districtList.addClass('required-entry').removeAttr('disabled');
                        self.districtList.show();
                        self.districtElem.hide();
                        self.districtLabel.addClass('required');
                    }else{
                        // self.districtList.removeClass('required-entry').prop('disabled', 'disabled').hide();
                        self.districtList.removeClass('required-entry').hide();
                        self.districtElem.show();
                        self.districtLabel.removeClass('required');
                    }
                    self.districtList.removeAttr('disable');
                    self._niceSelect(self.districtList, 'update');
                }
            });
        },

        _updatePostcode: function(district){
            if(this.options.currentDistrictJson){
                var postCodeInput = this.postcodeInput;
                $.each(this.options.currentDistrictJson, function (key, value) {
                    if(value.code==district){
                        postCodeInput.val(value.postcode);
                    }

                });
            }
        },

        /**
         * Check if the selected country has a mandatory region selection
         *
         * @param {String} country - Code of the country - 2 uppercase letter for country code
         * @private
         */
        _checkRegionRequired: function (country) {
            var self = this;

            this.options.isRegionRequired = false;
            $.each(this.options.regionJson.config['regions_required'], function (index, elem) {
                if (elem === country) {
                    self.options.isRegionRequired = true;
                }
            });
        }
    });

    return $.mage.regionsManager;
});
