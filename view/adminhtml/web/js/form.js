define([
    "jquery",
    "prototype",
    "mage/adminhtml/events",
    'domReady!'
], function(jQuery){
    window.CityUpdater = Class.create();
    CityUpdater.prototype = {
        initialize: function (countryEl, regionEl, cityTextEl, citySelectEl, cities, regions, disableAction, clearRegionValueOnDisable)
        {
            this.isCityRequired = true;
            this.countryEl = $(countryEl);
            this.regionEl = $(regionEl);
            this.cityTextEl = $(cityTextEl);
            this.citySelectEl = $(citySelectEl);
            this.config = cities['config'];
            delete cities.config;
            this.cities = cities;
            this.regions = regions;
            this.disableAction = (typeof disableAction=='undefined') ? 'hide' : disableAction;
            this.clearRegionValueOnDisable = (typeof clearRegionValueOnDisable == 'undefined') ? false : clearRegionValueOnDisable;

            if (this.citySelectEl.options.length <=1) {
                this.cityTextEl = $(cityTextEl);
                this.update();
            }
            else {
                this.lastRegionId = this.regionEl.value;
            }
            // this.cityTextEl.hide();
            this.regionEl.changeUpdater = this.update.bind(this);
            Event.observe(this.countryEl, 'change', this.update.bind(this));
            Event.observe(this.regionEl, 'change', this.update.bind(this));
            Event.observe(this.citySelectEl,'change',this.syncvalue.bind(this));
        },
        syncvalue:function(){
            this.cityTextEl.value = this.citySelectEl[this.citySelectEl.selectedIndex].text
        },
        _checkCityRequired: function()
        {
            if (!this.isCityRequired) {
                return;
            }

            var elements = [this.cityTextEl, this.citySelectEl];

            elements.each(function(currentElement) {
                if(!currentElement) {
                    return;
                }
                var form = currentElement.form,
                    validationInstance = form ? jQuery(form).data('validation') : null,
                    field = currentElement.up('.field') || new Element('div');

                if (validationInstance) {
                    validationInstance.clearError(currentElement);
                }

                //compute the need for the required fields
                if (!currentElement.visible()) {
                    if (field.hasClassName('required')) {
                        field.removeClassName('required');
                    }
                    if (currentElement.hasClassName('required-entry')) {
                        currentElement.removeClassName('required-entry');
                    }
                    if ('select' == currentElement.tagName.toLowerCase() &&
                        currentElement.hasClassName('validate-select')
                    ) {
                        currentElement.removeClassName('validate-select');
                    }
                } else {
                    if (!field.hasClassName('required')) {
                        field.addClassName('required');
                    }
                    if (!currentElement.hasClassName('required-entry')) {
                        currentElement.addClassName('required-entry');
                    }
                    if ('select' == currentElement.tagName.toLowerCase() &&
                        !currentElement.hasClassName('validate-select')
                    ) {
                        currentElement.addClassName('validate-select');
                    }
                }
            });
        },

        disableRegionValidation: function()
        {
            this.isCityRequired = false;
        },

        update: function()
        {
            if (this.cities[this.regionEl.value] && this.regions[this.countryEl.value]) {
                if (this.lastRegionId!=this.regionEl.value) {
                    var option, def;

                    def = this.citySelectEl.getAttribute('defaultValue');
                    if (this.cityTextEl) {
                        if (!def) {
                            def = this.cityTextEl.value.toLowerCase();
                        }
                        this.cityTextEl.value = '';
                    }

                    this.citySelectEl.options.length = 1;
                    for (cityId in this.cities[this.regionEl.value]) {
                        city = this.cities[this.regionEl.value][cityId];

                        option = document.createElement('OPTION');
                        option.value = cityId;
                        option.text = city['name'];
                        option.title = city['code'];

                        if (this.citySelectEl.options.add) {
                            this.citySelectEl.options.add(option);
                        } else {
                            this.citySelectEl.appendChild(option);
                        }

                        if (cityId==def || city['name'] ==  def || city['name'].toLowerCase()==def) {
                            this.citySelectEl.value = cityId;
                            this.cityTextEl.value = city['name'];
                        }
                    }
                }

                if (this.disableAction=='hide') {
                    if (this.cityTextEl) {
                        this.cityTextEl.style.display = 'none';
                        this.cityTextEl.style.disabled = true;
                    }
                    this.citySelectEl.style.display = '';
                    this.citySelectEl.disabled = false;
                } else if (this.disableAction=='disable') {
                    if (this.cityTextEl) {
                        this.cityTextEl.disabled = true;
                    }
                    this.citySelectEl.disabled = false;
                }
                this.setMarkDisplay(this.citySelectEl, true);

                this.lastRegionId = this.regionEl.value;
            } else {
                this.citySelectEl.value = '';
                if (this.disableAction=='hide') {
                    if (this.cityTextEl) {
                        this.cityTextEl.style.display = '';
                        this.cityTextEl.style.disabled = false;
                    }
                    this.citySelectEl.style.display = 'none';
                    this.citySelectEl.disabled = true;
                } else if (this.disableAction=='disable') {
                    if (this.cityTextEl) {
                        this.cityTextEl.disabled = false;
                    }
                    this.citySelectEl.disabled = true;
                    if (this.clearRegionValueOnDisable) {
                        this.citySelectEl.value = '';
                    }
                } else if (this.disableAction=='nullify') {
                    this.citySelectEl.options.length = 1;
                    this.citySelectEl.value = '';
                    this.citySelectEl.selectedIndex = 0;
                    this.lastRegionId = '';
                }
                this.setMarkDisplay(this.citySelectEl, false);

            }
            if(typeof this.citySelectEl.changeUpdater !== 'undefined'){
                this.citySelectEl.changeUpdater();
            }
            this._checkCityRequired();
        },

        setMarkDisplay: function(elem, display){
            if(elem.parentNode.parentNode){
                var marks = Element.select(elem.parentNode.parentNode, '.required');
                if(marks[0]){
                    display ? marks[0].show() : marks[0].hide();
                }
            }
        }
    };

    window.cityUpdater = CityUpdater;

    window.SubdistrictUpdater = Class.create();
    SubdistrictUpdater.prototype = {
        initialize: function (countryEl, regionEl, cityEl, subdistrictTextEl, subdistrictSelectEl, postcodeEl, regions, subdistricts, disableAction, clearRegionValueOnDisable)
        {
            this.isSubdistrictRequired = true;
            this.countryEl = $(countryEl);
            this.regionEl = $(regionEl);
            this.cityEl = $(cityEl);
            this.subdistrictTextEl = $(subdistrictTextEl);
            this.postcodeEl = $(postcodeEl);
            this.subdistrictSelectEl = $(subdistrictSelectEl);
            this.regions = regions;
            this.subdistricts = subdistricts;
            this.disableAction = (typeof disableAction=='undefined') ? 'hide' : disableAction;
            this.clearRegionValueOnDisable = (typeof clearRegionValueOnDisable == 'undefined') ? false : clearRegionValueOnDisable;

            if (this.subdistrictSelectEl.options.length<=1) {
                this.update();
            }
            else {
                this.lastRegionId = this.cityEl.value;
            }
            // this.subdistrictTextEl.hide();
            this.cityEl.changeUpdater = this.update.bind(this);
            Event.observe(this.subdistrictSelectEl,'change',this.syncvalue.bind(this));
            Event.observe(this.cityEl, 'change', this.update.bind(this));
            Event.observe(this.countryEl, 'change', this.update.bind(this));
            Event.observe(this.regionEl, 'change', this.update.bind(this));
        },
        syncvalue:function(){
            subdistrict = this.subdistricts[this.cityEl.value][this.subdistrictSelectEl.value];
            this.subdistrictTextEl.value = this.subdistrictSelectEl[this.subdistrictSelectEl.selectedIndex].text;
            this.postcodeEl.value = subdistrict['postcode'];
        },
        _checkSubdistrictRequired: function()
        {
            if (!this.isSubdistrictRequired) {
                return;
            }

            var elements = [this.subdistrictTextEl, this.subdistrictSelectEl];

            elements.each(function(currentElement) {
                if(!currentElement) {
                    return;
                }
                var form = currentElement.form,
                    validationInstance = form ? jQuery(form).data('validation') : null,
                    field = currentElement.up('.field') || new Element('div');

                if (validationInstance) {
                    validationInstance.clearError(currentElement);
                }

                //compute the need for the required fields
                if (!currentElement.visible()) {
                    if (field.hasClassName('required')) {
                        field.removeClassName('required');
                    }
                    if (currentElement.hasClassName('required-entry')) {
                        currentElement.removeClassName('required-entry');
                    }
                    if ('select' == currentElement.tagName.toLowerCase() &&
                        currentElement.hasClassName('validate-select')
                    ) {
                        currentElement.removeClassName('validate-select');
                    }
                } else {
                    if (!field.hasClassName('required')) {
                        field.addClassName('required');
                    }
                    if (!currentElement.hasClassName('required-entry')) {
                        currentElement.addClassName('required-entry');
                    }
                    if ('select' == currentElement.tagName.toLowerCase() &&
                        !currentElement.hasClassName('validate-select')
                    ) {
                        currentElement.addClassName('validate-select');
                    }
                }
            });
        },

        disableRegionValidation: function()
        {
            this.isSubdistrictRequired = false;
        },

        update: function()
        {
            if (typeof this.cityEl.value !== 'undefined' && this.subdistricts[this.cityEl.value]  ) {

                if (typeof this.cityEl.value !== 'undefined' && this.lastRegionId != this.cityEl.value) {
                    var i, option, region, def;

                    def = this.subdistrictSelectEl.getAttribute('defaultValue');
                    if (this.subdistrictTextEl) {
                        if (!def) {
                            def = this.subdistrictTextEl.value.toLowerCase();
                        }
                        this.subdistrictTextEl.value = '';
                    }

                    this.subdistrictSelectEl.options.length = 1;
                    for (subdistrictId in this.subdistricts[this.cityEl.value]) {
                        subdistrict = this.subdistricts[this.cityEl.value][subdistrictId];

                        option = document.createElement('OPTION');
                        option.value = subdistrictId;
                        option.text = subdistrict['name'];
                        option.title = subdistrict['code'];

                        if (this.subdistrictSelectEl.options.add) {
                            this.subdistrictSelectEl.options.add(option);
                        } else {
                            this.subdistrictSelectEl.appendChild(option);
                        }

                        if (subdistrictId==def || subdistrict['name'] ==  def || subdistrict['name'].toLowerCase()==def) {
                            this.subdistrictSelectEl.value = subdistrictId;
                            this.subdistrictTextEl.value = subdistrict['name'];
                        }
                    }
                }

                if (this.disableAction=='hide') {
                    if (this.subdistrictTextEl) {
                        this.subdistrictTextEl.style.display = 'none';
                        this.subdistrictTextEl.style.disabled = true;
                    }
                    this.subdistrictSelectEl.style.display = '';
                    this.subdistrictSelectEl.disabled = false;
                } else if (this.disableAction=='disable') {
                    if (this.subdistrictTextEl) {
                        this.subdistrictTextEl.disabled = true;
                    }
                    this.subdistrictSelectEl.disabled = false;
                }
                this.setMarkDisplay(this.subdistrictSelectEl, true);

                this.lastRegionId = this.cityEl.value;
            } else {
                this.subdistrictSelectEl.value = '';
                if (this.disableAction=='hide') {
                    if (this.subdistrictTextEl) {
                        this.subdistrictTextEl.style.display = '';
                        this.subdistrictTextEl.style.disabled = false;
                    }
                    this.subdistrictSelectEl.style.display = 'none';
                    this.subdistrictSelectEl.disabled = true;
                } else if (this.disableAction=='disable') {
                    if (this.subdistrictTextEl) {
                        this.subdistrictTextEl.disabled = false;
                    }
                    this.subdistrictSelectEl.disabled = true;
                    if (this.clearRegionValueOnDisable) {
                        this.subdistrictSelectEl.value = '';
                    }
                } else if (this.disableAction=='nullify') {
                    this.subdistrictSelectEl.options.length = 1;
                    this.subdistrictSelectEl.value = '';
                    this.subdistrictSelectEl.selectedIndex = 0;
                    this.lastRegionId = '';
                }
                this.setMarkDisplay(this.subdistrictSelectEl, false);

            }
            if(typeof this.subdistrictSelectEl.changeUpdater !== 'undefined'){
                this.subdistrictSelectEl.changeUpdater();
            }
            this._checkSubdistrictRequired();
        },

        setMarkDisplay: function(elem, display){
            if(elem.parentNode.parentNode){
                var marks = Element.select(elem.parentNode.parentNode, '.required');
                if(marks[0]){
                    display ? marks[0].show() : marks[0].hide();
                }
            }
        }
    };
    window.subdistrictUpdater = SubdistrictUpdater;

    window.ZipcodeUpdater = Class.create();
    ZipcodeUpdater.prototype = {
        initialize: function (subdistrictEl, postcodeTextEl, postcodeSelectEl, postcodes, disableAction, clearRegionValueOnDisable)
        {
            this.isRegionRequired = true;
            this.subdistrictEl = $(subdistrictEl);
            this.postcodeTextEl = $(postcodeTextEl);
            this.postcodeSelectEl = $(postcodeSelectEl);
            this.config = postcodes['config'];
            delete postcodes.config;
            this.postcodes = postcodes;
            this.disableAction = (typeof disableAction=='undefined') ? 'hide' : disableAction;
            this.clearRegionValueOnDisable = (typeof clearRegionValueOnDisable == 'undefined') ? false : clearRegionValueOnDisable;

            if (this.postcodeSelectEl.options.length<=1) {
                this.update();
            }
            else {
                this.lastRegionId = this.subdistrictEl.value;
            }
            // this.postcodeTextEl.hide();
            this.subdistrictEl.changeUpdater = this.update.bind(this);
            Event.observe(this.postcodeSelectEl,'change',this.syncvalue.bind(this));
            Event.observe(this.subdistrictEl, 'change', this.update.bind(this));
        },
        syncvalue:function(){
            if(this.postcodeSelectEl.value !== ""){
                this.postcodeTextEl.value = this.postcodeSelectEl[this.postcodeSelectEl.selectedIndex].text
            }
        },
        _checkRegionRequired: function()
        {
            if (!this.isRegionRequired) {
                return;
            }

            var label, wildCard;
            var elements = [this.postcodeTextEl, this.postcodeSelectEl];
            var that = this;
            if (typeof this.config == 'undefined') {
                return;
            }
            var regionRequired = this.config.regions_required.indexOf(this.subdistrictEl.value) >= 0;

            elements.each(function(currentElement) {
                if(!currentElement) {
                    return;
                }
                var form = currentElement.form,
                    validationInstance = form ? jQuery(form).data('validation') : null,
                    field = currentElement.up('.field') || new Element('div');

                if (validationInstance) {
                    validationInstance.clearError(currentElement);
                }
                label = $$('label[for="' + currentElement.id + '"]')[0];
                if (label) {
                    wildCard = label.down('em') || label.down('span.required');
                    var topElement = label.up('tr') || label.up('li');
                    if (!that.config.show_all_regions && topElement) {
                        if (regionRequired) {
                            topElement.show();
                        } else {
                            topElement.hide();
                        }
                    }
                }

                if (label && wildCard) {
                    if (!regionRequired) {
                        wildCard.hide();
                    } else {
                        wildCard.show();
                    }
                }

                //compute the need for the required fields
                if (!regionRequired || !currentElement.visible()) {
                    if (field.hasClassName('required')) {
                        field.removeClassName('required');
                    }
                    if (currentElement.hasClassName('required-entry')) {
                        currentElement.removeClassName('required-entry');
                    }
                    if ('select' == currentElement.tagName.toLowerCase() &&
                        currentElement.hasClassName('validate-select')
                    ) {
                        currentElement.removeClassName('validate-select');
                    }
                } else {
                    if (!field.hasClassName('required')) {
                        field.addClassName('required');
                    }
                    if (!currentElement.hasClassName('required-entry')) {
                        currentElement.addClassName('required-entry');
                    }
                    if ('select' == currentElement.tagName.toLowerCase() &&
                        !currentElement.hasClassName('validate-select')
                    ) {
                        currentElement.addClassName('validate-select');
                    }
                }
            });
        },

        disableRegionValidation: function()
        {
            this.isRegionRequired = false;
        },

        update: function()
        {
            if (this.postcodes[this.subdistrictEl.value]) {

                if (this.lastRegionId!=this.subdistrictEl.value) {
                    var i, option, region, def;

                    def = this.postcodeSelectEl.getAttribute('defaultValue');
                    if (this.postcodeTextEl) {
                        if (!def) {
                            def = this.postcodeTextEl.value.toLowerCase();
                        }
                        this.postcodeTextEl.value = '';
                    }

                    this.postcodeSelectEl.options.length = 1;
                    zipcode = this.postcodes[this.subdistrictEl.value][0];
                    // for (cityId in this.postcodes[this.subdistrictEl.value]) {


                    option = document.createElement('OPTION');
                    option.value = zipcode;
                    option.text = zipcode;
                    option.title = zipcode;

                    if (this.postcodeSelectEl.options.add) {
                        this.postcodeSelectEl.options.add(option);
                    } else {
                        this.postcodeSelectEl.appendChild(option);
                    }

                    if (zipcode==def) {
                        this.postcodeSelectEl.value = zipcode;
                        this.postcodeTextEl.value = zipcode;
                    }
                    // }
                }

                if (this.disableAction=='hide') {
                    if (this.postcodeTextEl) {
                        this.postcodeTextEl.style.display = 'none';
                        this.postcodeTextEl.style.disabled = true;
                    }
                    this.postcodeSelectEl.style.display = '';
                    this.postcodeSelectEl.disabled = false;
                } else if (this.disableAction=='disable') {
                    if (this.postcodeTextEl) {
                        this.postcodeTextEl.disabled = true;
                    }
                    this.postcodeSelectEl.disabled = false;
                }
                this.setMarkDisplay(this.postcodeSelectEl, true);

                this.lastRegionId = this.subdistrictEl.value;
            } else {
                if (this.disableAction=='hide') {
                    if (this.postcodeTextEl) {
                        this.postcodeTextEl.style.display = 'block';
                        this.postcodeTextEl.style.disabled = false;
                    }
                    this.postcodeSelectEl.style.display = 'none';
                    this.postcodeSelectEl.disabled = true;
                } else if (this.disableAction=='disable') {
                    if (this.postcodeTextEl) {
                        this.postcodeTextEl.disabled = false;
                    }
                    this.postcodeSelectEl.disabled = true;
                    if (this.clearRegionValueOnDisable) {
                        this.postcodeSelectEl.value = '';
                    }
                } else if (this.disableAction=='nullify') {
                    this.postcodeSelectEl.options.length = 1;
                    this.postcodeSelectEl.value = '';
                    this.postcodeSelectEl.selectedIndex = 0;
                    this.lastRegionId = '';
                }
                this.setMarkDisplay(this.postcodeSelectEl, false);
            }
        },

        setMarkDisplay: function(elem, display){
            if(elem.parentNode.parentNode){
                var marks = Element.select(elem.parentNode.parentNode, '.required');
                if(marks[0]){
                    display ? marks[0].show() : marks[0].hide();
                }
            }
        }
    };
    window.ZipcodeUpdater = ZipcodeUpdater;

});