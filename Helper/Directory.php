<?php

namespace Stableaddon\RegionalManagement\Helper;

/**
 * Class Directory
 *
 * @package Stableaddon\RegionalManagement\Helper
 */
class Directory extends \Magento\Directory\Helper\Data
{
    /**
     * Retrieve regions data json
     *
     * @return string
     */
    public function getRegionJson()
    {
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
        return $this->_regionJson;
    }

    /**
     * Retrieve regions data
     *
     * @return array
     */
    public function getRegionData()
    {
        $countryIds = [];
        foreach ($this->getCountryCollection() as $country) {
            $countryIds[] = $country->getCountryId();
        }
        $collection = $this->_regCollectionFactory->create();
        $collection->addCountryFilter($countryIds)->load();
        $regions = [
            'config' => [
                'show_all_regions' => $this->isShowNonRequiredState(),
                'regions_required' => $this->getCountriesWithStatesRequired(),
            ],
        ];
        foreach ($collection as $region) {
            /** @var $region \Magento\Directory\Model\Region */
            if (!$region->getRegionId()) {
                continue;
            }
            $regions[$region->getCountryId()][$region->getCountryId().'_'.$region->getRegionId()] = [
                'code' => $region->getCode(),
                'name' => (string)__($region->getName()),
            ];
        }
        return $regions;
    }
}
