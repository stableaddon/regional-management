# stableaddon-regional-management
Magento Regional Management provider the feature to manage region, district, sub district and show as dropdown on 
address form for Magento2.
 
# Installation
    Please use composer to install the extension.
    composer require stableaddon/regional-management

# Configuration
    Stores -> Configuration -> Customer -> Customer Configuration -> Address Templates
    
    Text: 
        {{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}
        {{depend company}}{{var company}}{{/depend}}
        {{if street1}}{{var street1}}
        {{/if}}
        {{depend street2}}{{var street2}}{{/depend}}
        {{depend street3}}{{var street3}}{{/depend}}
        {{depend street4}}{{var street4}}{{/depend}}
        {{if region}}{{var region}}, {{/if}}{{if city}}{{var city}},  {{/if}}{{if sub_district}}{{var sub_district}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}
        {{var country}}
        {{depend telephone}}T: {{var telephone}}{{/depend}}
        {{depend fax}}F: {{var fax}}{{/depend}}
        {{depend vat_id}}VAT: {{var vat_id}}{{/depend}}
         
    Text One Line:
        {{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}, {{var street}}, {{var region}}, {{var city}}, {{var sub_district}} {{var postcode}}, {{var country}}
        
    HTML:
        {{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}
        {{depend company}}{{var company}}{{/depend}}
        </br>{{if street1}}{{var street1}}
        {{/if}}
        {{depend street2}}{{var street2}}{{/depend}}
        {{depend street3}}{{var street3}}{{/depend}}
        {{depend street4}}{{var street4}}{{/depend}}
        {{if region}}{{var region}}, {{/if}}{{if city}}{{var city}},  {{/if}}{{if sub_district}}{{var sub_district}},  {{/if}}{{if postcode}}{{var postcode}}{{/if}}
        {{var country}}
        </br>{{depend telephone}}T: {{var telephone}}{{/depend}}
        {{depend fax}}F: {{var fax}}{{/depend}}
        {{depend vat_id}}VAT: {{var vat_id}}{{/depend}}
        
    PDF
        {{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}|
        {{depend company}}{{var company}}|{{/depend}}
        {{if street1}}{{var street1}}|{{/if}}
        {{depend street2}}{{var street2}}|{{/depend}}
        {{depend street3}}{{var street3}}|{{/depend}}
        {{depend street4}}{{var street4}}|{{/depend}}
        {{if region}}{{var region}}, {{/if}}{{if city}}{{var city}}, {{/if}}{{if sub_district}}{{var sub_district}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}|
        {{var country}}|
        {{depend telephone}}T: {{var telephone}}|{{/depend}}
        {{depend fax}}F: {{var fax}}|{{/depend}}|
        {{depend vat_id}}VAT: {{var vat_id}}{{/depend}}|
        
        
## Changelog
   
    * 1.0.0 
        - Show district, sub district as dropdown in address form.
        - Manage Region, District, Sub District in backend. 
        - Import region, district, sub district, import translate file.

        
## Compatible
    Magento version 2.3.x