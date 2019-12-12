<?php

namespace Stableaddon\RegionalManagement\Model\ResourceModel\SubDistrict;

/**
 * Class Collection
 *
 * @package Stableaddon\RegionalManagement\Model\ResourceModel\SubDistrict
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'district_id';

    /**
     * Locale region name table name
     *
     * @var string
     */
    protected $_districtNameTable;

    /**
     * Country table name
     *
     * @var string
     */
    protected $_cityTable;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param mixed $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    )
    {
        $this->_localeResolver = $localeResolver;
        $this->_resource = $resource;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Define main, country, locale region name tables
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Stableaddon\RegionalManagement\Model\SubDistrict::class, \Stableaddon\RegionalManagement\Model\ResourceModel\SubDistrict::class);

        $this->_cityTable = $this->getTable('directory_region_city');
        $this->_districtNameTable = $this->getTable('directory_city_district_name');

        $this->addOrder('name', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
        $this->addOrder('default_name', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
    }

    /**
     * Initialize select object
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $locale = $this->_localeResolver->getLocale();

        $this->addBindParam(':district_locale', $locale);
        $this->getSelect()->joinLeft(
            ['rname' => $this->_districtNameTable],
            'main_table.district_id = rname.district_id AND rname.locale = :district_locale',
            ['name']
        )->group('main_table.district_id');

        return $this;
    }


    /**
     * Filter by region name
     *
     * @param string|array $districtName
     *
     * @return $this
     */
    public function addDistrictNameFilter($districtName)
    {
        if (!empty($districtName)) {
            if (is_array($districtName)) {
                $this->addFieldToFilter('main_table.default_name', ['in' => $districtName]);
            } else {
                $this->addFieldToFilter('main_table.default_name', $districtName);
            }
        }

        return $this;
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $propertyMap = [
            'value' => 'district_id',
            'title' => 'default_name',
            'label' => 'default_name',
            'city_id' => 'city_id',
            'postcode' => 'postcode'
        ];

        foreach ($this as $item) {
            $option = [];
            foreach ($propertyMap as $code => $field) {
                $option[$code] = $item->getData($field);
            }
            $options[] = $option;
        }

        if (count($options) > 0) {
            array_unshift(
                $options,
                ['title' => '', 'value' => '', 'label' => __('Please select a sub district.')]
            );
        }

        return $options;
    }

}
