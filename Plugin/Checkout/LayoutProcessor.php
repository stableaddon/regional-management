<?php

namespace Stableaddon\RegionalManagement\Plugin\Checkout;

/**
 * Class LayoutProcessor
 * @package Stableaddon\RegionalManagement\Plugin\Checkout
 */
class LayoutProcessor
{
    /**
     *
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['city'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input'
            ],
            'dataScope' => 'shippingAddress.city',
            'source' => 'shippingAddress.city',
            'label' => __('City'),
            'provider' => 'checkoutProvider',
            'validation' => [
                'required-entry' => true,
            ],
            'sortOrder' => 90,
            'filterBy' => null,
            'customEntry' => null,
            'visible' => false,
            'placeholder' => __('City')
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['company']['visible'] = false;

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['sortOrder'] = 70;

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['city_id'] = [
            'component' => 'Stableaddon_RegionalManagement/js/form/element/city',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes',
                'mainScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'customEntry' => 'shippingAddress.city',
            ],
            'filterBy' => [
                'target' => '${ $.provider }:${ $.mainScope }.region_id',
                'field' => 'region_id',
            ],
            'label' => __('City'),
            'validation' => [
                'validate-select' => true,
            ],
            'provider' => 'checkoutProvider',
            'dataScope' => 'shippingAddress.custom_attributes.city_id',
            'source' => 'shippingAddress.custom_attributes.city_id',
            'sortOrder' => 90,
            'visible' => true,
            'placeholder' => __('City'),
            'imports' => [
                'initialOptions' => 'index = ${ $.provider }:dictionaries.city_id',
                'setOptions' => 'index = ${ $.provider }:dictionaries.city_id'
            ]
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['sub_district'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input'
            ],
            'dataScope' => 'shippingAddress.custom_attributes.sub_district',
            'source' => 'shippingAddress.custom_attributes.sub_district',
            'label' => __('Sub District'),
            'provider' => 'checkoutProvider',
            'validation' => [
                'required-entry' => true,
            ],
            'sortOrder' => 95,
            'filterBy' => null,
            'customEntry' => null,
            'visible' => false,
            'placeholder' => __('Sub District')
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['sub_district']['visible'] = false;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['sub_district_id'] = [
            'component' => 'Stableaddon_RegionalManagement/js/form/element/sub-district',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'customEntry' => 'shippingAddress.sub_district',
            ],
            'filterBy' => [
                'target' => '${ $.provider }:${ $.parentScope }.city_id',
                'field' => 'city_id',
            ],
            'label' => __('Sub District'),
            'validation' => [
                'validate-select' => true,
            ],
            'provider' => 'checkoutProvider',
            'dataScope' => 'shippingAddress.custom_attributes.sub_district_id',
            'source' => 'shippingAddress.custom_attributes.sub_district_id',
            'sortOrder' => 95,
            'visible' => true,
            'placeholder' => __('Sub District'),
            'imports' => [
                'initialOptions' => 'index = ${ $.provider }:dictionaries.sub_district_id',
                'setOptions' => 'index = ${ $.provider }:dictionaries.sub_district_id'
            ]
        ];
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['country_id']['placeholder'] = __('Country');
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['region']['placeholder'] = __('Region');



       /* BILLING ADDRESS */

        $configuration = $jsLayout['components']['checkout']['children']['steps']['children']
                        ['billing-step']['children']['payment']['children']['payments-list']['children'];
        foreach ($configuration as $paymentGroup => $groupConfig) {
            $scope = $paymentGroup;
            if (strpos($paymentGroup, 'form')) {
                $scope = str_replace("-form","",$paymentGroup);
            }

            if (isset($groupConfig['component'])
                && $groupConfig['component'] === 'Magento_Checkout/js/view/billing-address') {
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]['children']
                ['form-fields']['children']['country_id']['sortOrder'] = 80;

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]['children']
                ['form-fields']['children']['region_id']['sortOrder'] = 50;

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]['children']
                ['form-fields']['children']['telephone']['validation'] =
                    [
                        'validate-length' => 10,
                        'min_text_length' => 10,
                        'max_text_length' => 10,
                        'validate-number' => true,
                        'required-entry' =>  true
                    ];

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]['children']
                ['form-fields']['children']['city']['visible'] = false;

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]['children']
                ['form-fields']['children']['company']['visible'] = false;

                /* BILLING ADDRESS STREET */
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]['children']
                ['form-fields']['children']['street']['sortOrder'] = 50;

                /* BILLING ADDRESS COUNTRY */
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]['children']
                ['form-fields']['children']['country_id']['sortOrder'] = 115;

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]
                ['children']['form-fields']['children']['city'] = [
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'config' => [
                        'customScope' => 'billingAddress'.$scope.'',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input'
                    ],
                    'dataScope' => 'billingAddress'.$scope.'.city',
                    'label' => __('City'),
                    'provider' => 'checkoutProvider',
                    'validation' => [
                        'required-entry' => true,
                    ],
                    'sortOrder' => 60,
                    'filterBy' => null,
                    'customEntry' => null,
                    'visible' => false,
                ];

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]
                ['children']['form-fields']['children']['city_id'] = [
                    'label' => 'City',
                    'component' => 'Stableaddon_RegionalManagement/js/form/element/city',
                    'config' => [
                        'customScope' => 'billingAddress'.$scope.'.custom_attributes',
                        'mainScope' => 'billingAddress'.$scope,
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/select',
                        'customEntry' => 'billingAddress'.$scope.'.city'
                    ],
                    'filterBy' => [
                        'target' => '${ $.provider }:${ $.mainScope }.region_id',
                        'field' => 'region_id',
                    ],
                    'validation' => [
                        'validate-select' => true,
                    ],
                    'provider' => 'checkoutProvider',
                    'dataScope' => 'billingAddress'.$scope.'.custom_attributes.city_id',
                    'sortOrder' => 60,
                    'visible' => true,
                    'imports' => [
                        'initialOptions' => 'index = ${ $.provider }:dictionaries.city_id',
                        'setOptions' => 'index = ${ $.provider }:dictionaries.city_id'
                    ]
                ];

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]
                ['children']['form-fields']['children']['sub_district'] = [
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'config' => [
                        'customScope' => 'billingAddress'.$scope.'.custom_attributes',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input'
                    ],
                    'dataScope' => 'billingAddress'.$scope.'.custom_attributes.sub_district',
                    'label' => __('Sub District'),
                    'provider' => 'checkoutProvider',
                    'validation' => [
                        'required-entry' => true,
                    ],
                    'sortOrder' => 65,
                    'filterBy' => null,
                    'customEntry' => null,
                    'visible' => false,
                ];

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]
                ['children']['form-fields']['children']['sub_district']['visible'] = false;

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]
                ['children']['form-fields']['children']['sub_district_id'] = [
                    'label' => __('Sub District'),
                    'component' => 'Stableaddon_RegionalManagement/js/form/element/sub-district',
                    'config' => [
                        'customScope' => 'billingAddress'.$scope.'.custom_attributes',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/select',
                        'customEntry' => 'billingAddress'.$scope.'.sub_district'
                    ],
                    'filterBy' => [
                        'target' => '${ $.provider }:${ $.parentScope }.city_id',
                        'field' => 'city_id',
                    ],
                    'validation' => [
                        'validate-select' => true,
                    ],
                    'provider' => 'checkoutProvider',
                    'dataScope' => 'billingAddress'.$scope.'.custom_attributes.sub_district_id',
                    'sortOrder' => 65,
                    'visible' => true,
                    'imports' => [
                        'initialOptions' => 'index = ${ $.provider }:dictionaries.sub_district_id',
                        'setOptions' => 'index = ${ $.provider }:dictionaries.sub_district_id'
                    ]
                ];
            }
        }

        return $jsLayout;
    }
}