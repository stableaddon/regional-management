<?php

namespace Stableaddon\RegionalManagement\Block\Adminhtml\Order\Create\Shipping;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session\Quote as BackendQuote;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Helper\Address as HelperAddress;
use Magento\Customer\Model\Address\Mapper;
use Magento\Customer\Model\Metadata\FormFactory as MetadataFormFactory;
use Magento\Customer\Model\Options;
use Magento\Directory\Helper\Data;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Address as ShippingAddress;
use Magento\Sales\Model\AdminOrder\Create;

/**
 * Class Address
 *
 * @package Stableaddon\RegionalManagement\Block\Adminhtml\Order\Create\Shipping
 */
class Address extends ShippingAddress
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * layout
     *
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * Customer form factory
     *
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $_customerFormFactory;

    /**
     * Json encoder
     *
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * Directory helper
     *
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * Customer options
     *
     * @var \Magento\Customer\Model\Options
     */
    protected $options;

    /**
     * Address service
     *
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressService;

    /**
     * Address helper
     *
     * @var \Magento\Customer\Helper\Address
     */
    protected $_addressHelper;

    /**
     * Search criteria builder
     *
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Filter builder
     *
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Customer\Model\Address\Mapper
     */
    protected $addressMapper;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    private $countriesCollection;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    private $backendQuoteSession;

    /**
     * Address constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Customer\Model\Metadata\FormFactory $customerFormFactory
     * @param \Magento\Customer\Model\Options $options
     * @param \Magento\Customer\Helper\Address $addressHelper
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressService
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Customer\Model\Address\Mapper $addressMapper
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param array $data
     */
    public function __construct(
        Context $context,
        BackendQuote $sessionQuote,
        Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        FormFactory $formFactory,
        DataObjectProcessor $dataObjectProcessor,
        Data $directoryHelper,
        EncoderInterface $jsonEncoder,
        MetadataFormFactory $customerFormFactory,
        Options $options,
        HelperAddress $addressHelper,
        AddressRepositoryInterface $addressService,
        SearchCriteriaBuilder $criteriaBuilder,
        FilterBuilder $filterBuilder,
        Mapper $addressMapper,
        ResourceConnection $resource,
        LayoutInterface $layout,
        array $data = []
    ) {
        $this->layout = $layout;
        $this->resource = $resource;

        parent::__construct(
            $context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $formFactory,
            $dataObjectProcessor,
            $directoryHelper,
            $jsonEncoder,
            $customerFormFactory,
            $options,
            $addressHelper,
            $addressService,
            $criteriaBuilder,
            $filterBuilder,
            $addressMapper,
            $data
        );
    }

    /**
     * Prepare Form and add elements to form
     *
     * @return $this
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _prepareForm()
    {
        $this->setJsVariablePrefix('shippingAddress');
        $fieldset = $this->_form->addFieldset('main', ['no_container' => true]);

        $addressForm = $this->_customerFormFactory->create('customer_address', 'adminhtml_customer_address');
        $attributes = $addressForm->getAttributes();
        $this->_addAttributesToForm($attributes, $fieldset);

        $prefixElement = $this->_form->getElement('prefix');
        if ($prefixElement) {
            $prefixOptions = $this->options->getNamePrefixOptions($this->getStore());
            if (!empty($prefixOptions)) {
                $fieldset->removeField($prefixElement->getId());
                $prefixField = $fieldset->addField($prefixElement->getId(), 'select', $prefixElement->getData(), '^');
                $prefixField->setValues($prefixOptions);
                if ($this->getAddressId()) {
                    $prefixField->addElementValues($this->getAddress()->getPrefix());
                }
            }
        }

        $suffixElement = $this->_form->getElement('suffix');
        if ($suffixElement) {
            $suffixOptions = $this->options->getNameSuffixOptions($this->getStore());
            if (!empty($suffixOptions)) {
                $fieldset->removeField($suffixElement->getId());
                $suffixField = $fieldset->addField(
                    $suffixElement->getId(),
                    'select',
                    $suffixElement->getData(),
                    $this->_form->getElement('lastname')->getId()
                );
                $suffixField->setValues($suffixOptions);
                if ($this->getAddressId()) {
                    $suffixField->addElementValues($this->getAddress()->getSuffix());
                }
            }
        }

        $regionElement = $this->_form->getElement('region_id');
        if ($regionElement) {
            $regionElement->setNoDisplay(true);
        }
        $regionCity = $this->_form->getElement('city_id');
        if ($regionCity) {
            $regionCity->setNoDisplay(true);
        }
        $regionSubdistrict = $this->_form->getElement('sub_district_id');
        if ($regionSubdistrict) {
            $regionSubdistrict->setNoDisplay(true);
        }

        $this->_form->setValues($this->getFormValues());

        if ($this->_form->getElement('country_id')->getValue()) {
            $countryId = $this->_form->getElement('country_id')->getValue();
            $this->_form->getElement('country_id')->setValue(null);
            foreach ($this->_form->getElement('country_id')->getValues() as $country) {
                if ($country['value'] == $countryId) {
                    $this->_form->getElement('country_id')->setValue($countryId);
                }
            }
        }
        if ($this->_form->getElement('country_id')->getValue() === null) {
            $this->_form->getElement('country_id')->setValue(
                $this->directoryHelper->getDefaultCountry($this->getStore())
            );
        }
        $this->processCountryOptions($this->_form->getElement('country_id'));
        // Set custom renderer for VAT field if needed
        $vatIdElement = $this->_form->getElement('vat_id');
        if ($vatIdElement && $this->getDisplayVatValidationButton() !== false) {
            $vatIdElement->setRenderer(
                $this->layout->createBlock(
                    \Magento\Customer\Block\Adminhtml\Sales\Order\Address\Form\Renderer\Vat::class
                )->setJsVariablePrefix(
                    $this->getJsVariablePrefix()
                )
            );
        }

        $this->_form->addFieldNameSuffix('order[shipping_address]');
        $this->_form->setHtmlNamePrefix('order[shipping_address]');
        $this->_form->setHtmlIdPrefix('order-shipping_address_');

        return $this;
    }

    /**
     * Add rendering EAV attributes to Form element
     *
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface[] $attributes
     * @param \Magento\Framework\Data\Form\AbstractForm $form
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _addAttributesToForm($attributes, \Magento\Framework\Data\Form\AbstractForm $form)
    {
        // add additional form types
        $types = $this->_getAdditionalFormElementTypes();
        foreach ($types as $type => $className) {
            $form->addType($type, $className);
        }
        $renderers = $this->_getAdditionalFormElementRenderers();

        foreach ($attributes as $attribute) {
            $inputType = $attribute->getFrontendInput();

            if ($inputType) {
                $element = $form->addField(
                    $attribute->getAttributeCode(),
                    $inputType,
                    [
                        'name' => $attribute->getAttributeCode(),
                        'label' => __($attribute->getStoreLabel()),
                        'class' => $attribute->getFrontendClass(),
                        'required' => $attribute->isRequired()
                    ]
                );
                if ($inputType == 'multiline') {
                    $element->setLineCount($attribute->getMultilineCount());
                }
                $element->setEntityAttribute($attribute);
                $this->_addAdditionalFormElementData($element);

                if (!empty($renderers[$attribute->getAttributeCode()])) {
                    $element->setRenderer($renderers[$attribute->getAttributeCode()]);
                }

                if ($inputType == 'select' || $inputType == 'multiselect') {
                    $options = [];
                    foreach ($attribute->getOptions() as $optionData) {
                        $data = $this->dataObjectProcessor->buildOutputDataArray(
                            $optionData,
                            \Magento\Customer\Api\Data\OptionInterface::class
                        );
                        foreach ($data as $key => $value) {
                            if (is_array($value)) {
                                unset($data[$key]);
                                $data['value'] = $value;
                            }
                        }
                        $options[] = $data;
                    }
                    $element->setValues($options);
                } elseif ($inputType == 'date') {
                    $format = $this->_localeDate->getDateFormat(
                        \IntlDateFormatter::SHORT
                    );
                    $element->setDateFormat($format);
                }
            }
        }

        return $this;
    }
    /**
     * Return array of additional form element renderers by element id
     *
     * @return array
     */
    protected function _getAdditionalFormElementRenderers()
    {
        $address = $this->getAddress();
        return [
            'region' => $this->layout->createBlock(
                \Magento\Customer\Block\Adminhtml\Edit\Renderer\Region::class
            ),
            'city' => $this->layout->createBlock(
                \Stableaddon\RegionalManagement\Block\Adminhtml\Sales\Order\Address\Edit\Renderer\City::class
            )->setCityId($address->getCityId() ? $address->getCityId() : ''),
            'sub_district' => $this->layout->createBlock(
                \Stableaddon\RegionalManagement\Block\Adminhtml\Sales\Order\Address\Edit\Renderer\Subdistrict::class
            )->setSubDistrictId($address->getSubDistrictId() ? $address->getSubDistrictId() : '')
        ];
    }

    /**
     * Return Customer Address Collection as JSON
     *
     * @return string
     */
    public function getAddressCollectionJson()
    {
        $connection = $this->resource->getConnection();
        $table = $this->resource->getTableName('customer_address_entity');

        $defaultCountryId = $this->directoryHelper->getDefaultCountry($this->getStore());
        $emptyAddressForm = $this->_customerFormFactory->create(
            'customer_address',
            'adminhtml_customer_address',
            [\Magento\Customer\Api\Data\AddressInterface::COUNTRY_ID => $defaultCountryId]
        );
        $data = [0 => $emptyAddressForm->outputData(\Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_JSON)];
        foreach ($this->getAddressCollection() as $address) {
            $addressForm = $this->_customerFormFactory->create(
                'customer_address',
                'adminhtml_customer_address',
                $this->addressMapper->toFlatArray($address)
            );
            $data[$address->getId()] = $addressForm->outputData(
                \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_JSON
            );
            $cityId = $connection->fetchOne('select city_id from ' . $table . ' where entity_id=?', [$address->getId()]);
            $subDistrictId = $connection->fetchOne('select sub_district_id from ' . $table . ' where entity_id=?', [$address->getId()]);
            $data[$address->getId()]['city_id'] = $cityId;
            $data[$address->getId()]['sub_district_id'] = $subDistrictId;
        }

        return $this->_jsonEncoder->encode($data);
    }

    /**
     * Retrieve Directory Countries collection
     *
     * @return \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    private function getCountriesCollection()
    {
        if (!$this->countriesCollection) {
            $this->countriesCollection = ObjectManager::getInstance()
                ->get(\Magento\Directory\Model\ResourceModel\Country\Collection::class);
        }

        return $this->countriesCollection;
    }

    /**
     * Retrieve Backend Quote Session
     *
     * @return Quote
     */
    private function getBackendQuoteSession()
    {
        if (!$this->backendQuoteSession) {
            $this->backendQuoteSession = ObjectManager::getInstance()->get(Quote::class);
        }

        return $this->backendQuoteSession;
    }

    /**
     * Process country options.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $countryElement
     * @return void
     */
    private function processCountryOptions(\Magento\Framework\Data\Form\Element\AbstractElement $countryElement)
    {
        $storeId = $this->getBackendQuoteSession()->getStoreId();
        $options = $this->getCountriesCollection()
            ->loadByStore($storeId)
            ->toOptionArray();

        $countryElement->setValues($options);
    }
}