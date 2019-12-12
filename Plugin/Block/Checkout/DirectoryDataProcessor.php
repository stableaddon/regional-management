<?php

namespace Stableaddon\RegionalManagement\Plugin\Block\Checkout;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Api\StoreResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class DirectoryDataProcessor
 *
 * @package Stableaddon\RegionalManagement\Plugin\Block\Checkout
 */
class DirectoryDataProcessor
{

    /**
     * @var array
     */
    private $cityOptions;

    /**
     * @var array
     */
    private $subDistrictOptions;

    /**
     * @var \Stableaddon\RegionalManagement\Model\ResourceModel\City\CollectionFactory
     */
    private $cityCollectionFactory;

    /**
     * @var \Stableaddon\RegionalManagement\Model\ResourceModel\SubDistrict\CollectionFactory
     */
    private $subDistrictCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DirectoryHelper
     */
    private $directoryHelper;

    /**
     * DirectoryDataProcessor constructor.
     *
     * @param \Stableaddon\RegionalManagement\Model\ResourceModel\City\CollectionFactory $cityCollection
     * @param \Stableaddon\RegionalManagement\Model\ResourceModel\SubDistrict\CollectionFactory $subDistrictCollection
     * @param \Magento\Store\Api\StoreResolverInterface $storeResolver
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Store\Model\StoreManagerInterface|null $storeManager
     */
    public function __construct(
        \Stableaddon\RegionalManagement\Model\ResourceModel\City\CollectionFactory $cityCollection,
        \Stableaddon\RegionalManagement\Model\ResourceModel\SubDistrict\CollectionFactory $subDistrictCollection,
        StoreResolverInterface $storeResolver,
        DirectoryHelper $directoryHelper,
        StoreManagerInterface $storeManager = null
    )
    {
        $this->cityCollectionFactory = $cityCollection;
        $this->subDistrictCollectionFactory = $subDistrictCollection;
        $this->directoryHelper = $directoryHelper;
        $this->storeManager = $storeManager ?: ObjectManager::getInstance()->get(StoreManagerInterface::class);
    }

    /**
     * @param \Magento\Checkout\Block\Checkout\DirectoryDataProcessor $processor
     * @param $jsLayout
     *
     * @return mixed
     */
    public function afterProcess(\Magento\Checkout\Block\Checkout\DirectoryDataProcessor $processor, $jsLayout)
    {
        if (isset($jsLayout['components']['checkoutProvider']['dictionaries'])) {
            $jsLayout['components']['checkoutProvider']['dictionaries']['city_id'] = $this->getCityOptions();
            $jsLayout['components']['checkoutProvider']['dictionaries']['sub_district_id'] = $this->getSubDistrictOptions();
        }

        return $jsLayout;
    }

    /**
     * Get country options list.
     *
     * @return array
     */
    private function getCityOptions()
    {
        if (!isset($this->cityOptions)) {
            $this->cityOptions = $this->cityCollectionFactory->create()->toOptionArray();
        }

        return $this->cityOptions;
    }

    /**
     * @return array
     */
    private function getSubDistrictOptions()
    {
        if (!isset($this->subDistrictOptions)) {
            $this->subDistrictOptions = $this->subDistrictCollectionFactory->create()->toOptionArray();
        }

        return $this->subDistrictOptions;
    }
}