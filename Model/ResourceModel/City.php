<?php

namespace Stableaddon\RegionalManagement\Model\ResourceModel;

/**
 * Class City
 *
 * @package Stableaddon\RegionalManagement\Model\ResourceModel
 */
class City extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Table with localized region names
     *
     * @var string
     */
    protected $_cityNameTable;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_localeResolver = $localeResolver;
    }

    /**
     * Define main and locale region name tables
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('directory_region_city', 'city_id');
        $this->_cityNameTable = $this->getTable('directory_region_city_name');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $connection = $this->getConnection();

        $locale = $this->_localeResolver->getLocale();
        $systemLocale = \Magento\Framework\AppInterface::DISTRO_LOCALE_CODE;

        $regionField = $connection->quoteIdentifier($this->getMainTable() . '.' . $this->getIdFieldName());

        $condition = $connection->quoteInto('lrn.locale = ?', $locale);
        $select->joinLeft(
            ['lrn' => $this->_cityNameTable],
            "{$regionField} = lrn.city_id AND {$condition}",
            []
        );

        if ($locale != $systemLocale) {
            $nameExpr = $connection->getCheckSql('lrn.city_id is null', 'srn.name', 'lrn.name');
            $condition = $connection->quoteInto('srn.locale = ?', $systemLocale);
            $select->joinLeft(
                ['srn' => $this->_cityNameTable],
                "{$regionField} = srn.city_id AND {$condition}",
                ['name' => $nameExpr]
            );
        } else {
            $select->columns(['name'], 'lrn');
        }

        return $select;
    }

    /**
     * Load object by country id and code or default name
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param int $regionId
     * @param string $value
     * @param string $field
     * @return $this
     */
    protected function _loadByRegion($object, $regionId, $value, $field)
    {
        $connection = $this->getConnection();
        $locale = $this->_localeResolver->getLocale();
        $joinCondition = $connection->quoteInto('rname.city_id = region.city_id AND rname.locale = ?', $locale);
        $select = $connection->select()->from(
            ['city' => $this->getMainTable()]
        )->joinLeft(
            ['rname' => $this->getTable('directory_region_city_name')],
            $joinCondition,
            ['name']
        )->where(
            'city.region_id = ?',
            $regionId
        )->where(
            "city.{$field} = ?",
            $value
        );

        $data = $connection->fetchRow($select);
        if ($data) {
            $object->setData($data);
        }

        $this->_afterLoad($object);

        return $this;
    }

    /**
     * Load data by country id and default region name
     *
     * @param \Magento\Directory\Model\Region $region
     * @param string $regionName
     * @param string $regionId
     * @return $this
     */
    public function loadByName(\Stableaddon\RegionalManagement\Model\City $city, $cityName, $cityId)
    {
        return $this->_loadByRegion($city, $cityId, (string)$cityName, 'default_name');
    }
}
