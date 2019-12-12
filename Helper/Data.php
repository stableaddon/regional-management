<?php

namespace Stableaddon\RegionalManagement\Helper;

/**
 * Class Data
 *
 * @package Stableaddon\RegionalManagement\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var null
     */
    protected $subdistrictJson = null;

    /**
     * @var null
     */
    protected $cityJson = null;

    /**
     * @var null
     */
    protected $postcodeJson = null;

    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Customer\Model\CustomerFactory|null
     */
    protected $_customerFactory = null;

    /**
     * Json representation of regions data
     *
     * @var string
     */
    protected $_regionJson;

    /**
     * Country collection
     *
     * @var \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    protected $_cityCollectionFactory;

    /**
     * Region collection
     *
     * @var \Magento\Directory\Model\ResourceModel\Region\Collection
     */
    protected $_districtCollection;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    protected $_cityCollection;

    /**
     * Json representation of regions data
     *
     * @var string
     */
    protected $_cityJson;

    /**
     * @var null
     */
    protected $_districtJson = null;

    /**
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
    protected $_districtCollectionFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;


    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
    protected $_regCollectionFactory;

    /**
     * @var \Magento\Framework\DataObject\Copy\Config
     */
    protected $fieldsetConfig;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Stableaddon\RegionalManagement\Model\ResourceModel\City\CollectionFactory $cityCollectionFactory
     * @param \Stableaddon\RegionalManagement\Model\ResourceModel\SubDistrict\CollectionFactory $districtCollectionFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regCollectionFactory
     * @param \Magento\Framework\DataObject\Copy\Config $fieldsetConfig
     * @param \Magento\Directory\Helper\Data $directoryHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Stableaddon\RegionalManagement\Model\ResourceModel\City\CollectionFactory $cityCollectionFactory,
        \Stableaddon\RegionalManagement\Model\ResourceModel\SubDistrict\CollectionFactory $districtCollectionFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regCollectionFactory,
        \Magento\Framework\DataObject\Copy\Config $fieldsetConfig,
        \Magento\Directory\Helper\Data $directoryHelper
    )
    {
        parent::__construct($context);
        $this->_configCacheType = $configCacheType;
        $this->_cityCollectionFactory = $cityCollectionFactory;
        $this->_districtCollectionFactory = $districtCollectionFactory;
        $this->jsonHelper = $jsonHelper;
        $this->directoryHelper = $directoryHelper;
        $this->_storeManager = $storeManager;
        $this->coreRegistry = $coreRegistry;
        $this->_customerFactory = $customerFactory;
        $this->fieldsetConfig = $fieldsetConfig;
        $this->_regCollectionFactory = $regCollectionFactory;
    }

    /**
     * @param string $fieldset
     * @param string $root
     *
     * @return array
     */
    public function getExtraCheckoutAddressFields($fieldset = 'subdistrict_checkout_billing_address_fields', $root = 'global')
    {
        $fields = $this->fieldsetConfig->getFieldset($fieldset, $root);

        $extraCheckoutFields = [];

        foreach ($fields as $field => $fieldInfo) {
            $extraCheckoutFields[] = $field;
        }

        return $extraCheckoutFields;
    }

    /**
     * @param $customerId
     * @param $addressId
     *
     * @return bool
     */
    public function getAddressData($customerId, $addressId)
    {
        $customer = $this->_customerFactory->create();
        $customer->load($customerId);
        $addresses = $customer->getAddresses();
        foreach ($addresses as $address) {
            if ($address->getId() === $addressId) {
                return $address->getData();
            }
        }

        return false;
    }

    /**
     * @return \Magento\Directory\Model\ResourceModel\Region\Collection
     */
    public function getDistrictCollection()
    {
        if (!$this->_districtCollection) {
            $this->_districtCollection = $this->_districtCollectionFactory->create();
            $this->_districtCollection->load();
        }

        return $this->_districtCollection;
    }

    /**
     * @return \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    public function getCityCollection()
    {
        if (!$this->_cityCollection) {
            $this->_cityCollection = $this->_cityCollectionFactory->create();
            $this->_cityCollection->load();
        }

        return $this->_cityCollection;
    }

    /**
     * @return string
     */
    public function getCityJson()
    {
        \Magento\Framework\Profiler::start('TEST: ' . __METHOD__, ['group' => 'TEST', 'method' => __METHOD__]);
        if (!$this->_cityJson) {
            $cacheKey = 'REGIONSMANAGER_CITY_JSON_STORE' . $this->_storeManager->getStore()->getId();
            $json = $this->_configCacheType->load($cacheKey);
            if (empty($json)) {
                $regions = $this->getCityData();
                $json = $this->jsonHelper->jsonEncode($regions);
                if ($json === false) {
                    $json = 'false';
                }
                $this->_configCacheType->save($json, $cacheKey);
            }
            $this->_cityJson = $json;
        }

        \Magento\Framework\Profiler::stop('TEST: ' . __METHOD__);

        return $this->_cityJson;
    }


    /**
     * Retrieve regions data
     *
     * @return array
     */
    public function getCityData()
    {
        $collection = $this->getCityCollection();
        $regions = [];
        foreach ($collection as $region) {
            /** @var $region \Magento\Directory\Model\Region */
            $regions[$region->getRegionId()][$region->getId()] = [
                'code' => $region->getId(),
                'name' => (string)__($region->getName()),
            ];
        }

        return $regions;
    }

    /**
     * @return null|string
     */
    public function getDistrictJson()
    {
        \Magento\Framework\Profiler::start('TEST: ' . __METHOD__, ['group' => 'TEST', 'method' => __METHOD__]);
        if (!$this->_districtJson) {
            $cacheKey = 'REGIONSMANAGER_DISTRICT_JSON_STORE' . $this->_storeManager->getStore()->getId();
            $json = $this->_configCacheType->load($cacheKey);
            if (empty($json)) {
                $regions = $this->getDistrictData();
                $json = $this->jsonHelper->jsonEncode($regions);
                if ($json === false) {
                    $json = 'false';
                }
                $this->_configCacheType->save($json, $cacheKey);
            }
            $this->_districtJson = $json;
        }

        \Magento\Framework\Profiler::stop('TEST: ' . __METHOD__);

        return $this->_districtJson;
    }


    /**
     * Retrieve regions data
     *
     * @return array
     */
    public function getDistrictData()
    {
        $collection = $this->getDistrictCollection();
        $regions = [
        ];
        foreach ($collection as $region) {
            /** @var $region \Magento\Directory\Model\Region */
            $regions[$region->getCityId()][$region->getId()] = [
                'code' => $region->getId(),
                'name' => (string)__($region->getName()),
                'postcode' => $region->getPostcode()
            ];
        }

        return $regions;
    }

    /**
     * @param null $region
     *
     * @return null|string
     */
    public function getAdminCityJson($region = null)
    {
        if (!$this->cityJson) {
            $cacheKey = 'DIRECTORY_CITIES_JSON_STORE' . $this->_storeManager->getStore()->getId();
            $json = $this->_configCacheType->load($cacheKey);
            if (empty($json)) {
                $cities = [];
                foreach ($this->getCityCollection() as $city) {
                    $cities[$city->getRegionId()][$city->getId()] = array(
                        'code' => $city->getCode(),
                        'name' => $city->getName(),
                    );
                }
                $json = $this->jsonHelper->jsonEncode($cities);
                if ($json === false) {
                    $json = 'false';
                }
                $this->_configCacheType->save($json, $cacheKey);
            }
            $this->cityJson = $json;
        }
        if ($region != null && $region != '' && $region != 0) {
            $cities_data = $this->jsonHelper->jsonDecode($this->cityJson);

            return $this->jsonHelper->jsonEncode($cities_data[$region]);

        }

        return $this->cityJson;
    }

    /**
     * @param null $city
     *
     * @return null|string
     */
    public function getSubdistrictJson($city = null)
    {
        if (!$this->subdistrictJson) {
            $cacheKey = 'DIRECTORY_SUBDISTRICT_JSON_STORE' . $this->_storeManager->getStore()->getId();
            $json = $this->_configCacheType->load($cacheKey);
            if (empty($json)) {
                $subdistricts = [];
                $subdistricts = [
                    'config' => [
                        'show_all_regions' => 'true',
                        'regions_required' => 'true',
                    ],
                ];
                foreach ($this->getDistrictCollection() as $subdistrict) {
                    $subdistricts[$subdistrict->getCityId()][$subdistrict->getId()] = array(
                        'code' => $subdistrict->getCode(),
                        'name' => $subdistrict->getName(),
                        'postcode' => $subdistrict->getPostcode()
                    );
                }
                $json = $this->jsonHelper->jsonEncode($subdistricts);
                if ($json === false) {
                    $json = 'false';
                }
                $this->_configCacheType->save($json, $cacheKey);
            }
            $this->subdistrictJson = $json;
        }
        if ($city != null && $city != '' && $city != 0) {
            $subdistricts_data = $this->jsonHelper->jsonDecode($this->subdistrictJson);

            return $this->jsonHelper->jsonEncode($subdistricts_data[$city]);
        }

        return $this->subdistrictJson;
    }

    /**
     * @param null $subdistrict
     *
     * @return null|string
     */
    public function getPostcodeJson($subdistrict = null)
    {
        if (!$this->postcodeJson) {
            $cacheKey = 'DIRECTORY_POSTCODE_JSON_STORE' . $this->_storeManager->getStore()->getId();
            $json = $this->_configCacheType->load($cacheKey);
            if (empty($json)) {
                $postcodes = [];
                foreach ($this->getDistrictCollection() as $subdistricts) {
                    $value = explode(",", $subdistricts->getPostcode());
                    if (isset($value[0]) && $value[0]) {
                        $postcodes[$subdistricts->getId()] = $value[0];
                    } else {
                        $postcodes[$subdistricts->getId()] = $subdistricts->getPostcode();
                    }

                }
                $json = $this->jsonHelper->jsonEncode($postcodes);
                if ($json === false) {
                    $json = 'false';
                }
                $this->_configCacheType->save($json, $cacheKey);
            }
            $this->postcodeJson = $json;
        }
        if ($subdistrict != null && $subdistrict != '' && $subdistrict != 0) {
            $postcodes_data = $this->jsonHelper->jsonDecode($this->postcodeJson);

            return $this->jsonHelper->jsonEncode($postcodes_data[$subdistrict]);
        }

        return $this->postcodeJson;
    }

    /**
     * @return mixed
     */
    public function getCustomerAddresses()
    {
        $result = array();
        $customerId = $this->coreRegistry->registry('current_customer_id');

        $isExistingCustomer = (bool)$customerId;
        for ($i = 0; $i <= 20; $i++) {
            $result['new_' . $i] = array(
                'appliedchange' => 0,
                'city' => '',
                'barangay' => ''
            );
        }
        if ($isExistingCustomer) {
            try {
                $customer = $this->_customerFactory->create();
                $customer->load($customerId);
                $addresses = $customer->getAddresses();
                foreach ($addresses as $address) {
                    $address->setAppliedchange(0);
                    $result[$address->getId()] = $address->getData();
                }

            } catch (\Exception $e) {

            }
        }

        return $this->jsonHelper->jsonEncode($result);
    }

    /**
     * @param $customerId
     * @param $addressId
     *
     * @return bool
     */
    public function getAddressSubDistrict($customerId, $addressId)
    {
        $customer = $this->_customerFactory->create();
        $customer->load($customerId);
        $addresses = $customer->getAddresses();
        foreach ($addresses as $address) {
            if ($address->getId() === $addressId) {
                return $address->getSubDistrict();
            }
        }

        return false;
    }

    /**
     * Retrieve regions data
     *
     * @return array
     */
    public function getRegionData()
    {
        $countryIds = [];
        foreach ($this->directoryHelper->getCountryCollection() as $country) {
            $countryIds[] = $country->getCountryId();
        }
        $collection = $this->_regCollectionFactory->create();
        $collection->addCountryFilter($countryIds)->load();
        $regions = [
            'config' => [
                'show_all_regions' => $this->directoryHelper->isShowNonRequiredState(),
                'regions_required' => $this->directoryHelper->getCountriesWithStatesRequired(),
            ],
        ];
        $k = 0;
        foreach ($collection as $region) {
            /** @var $region \Magento\Directory\Model\Region */
            if (!$region->getRegionId()) {
                continue;
            }
            $regions[$region->getCountryId()][$k] = [
                'id' => $region->getRegionId(),
                'code' => $region->getCode(),
                'name' => (string)__($region->getName()),
            ];
            $k++;
        }

        return $regions;
    }

    /**
     * Retrieve regions data json
     *
     * @return string
     */
    public function getRegionJson()
    {
        \Magento\Framework\Profiler::start('TEST: ' . __METHOD__, ['group' => 'TEST', 'method' => __METHOD__]);
        if (!$this->_regionJson) {
            $cacheKey = 'DIRECTORY_REGIONS_JSON_STORE' . $this->_storeManager->getStore()->getId();
            $json = $this->_configCacheType->load($cacheKey);
            if (empty($json)) {
                $regions = $this->getRegionData();
                $json = $this->jsonHelper->jsonEncode($regions);
                if ($json === false) {
                    $json = 'false';
                }
                $this->_configCacheType->save($json, $cacheKey);
            }
            $this->_regionJson = $json;
        }

        \Magento\Framework\Profiler::stop('TEST: ' . __METHOD__);

        return $this->_regionJson;
    }
}
