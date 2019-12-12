<?php

namespace Stableaddon\RegionalManagement\Model\ResourceModel;

/**
 * Class SubDistrict
 *
 * @package Stableaddon\RegionalManagement\Model\ResourceModel
 */
class SubDistrict extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_idFieldName = 'district_id';
    /**
     * Table with localized region names
     *
     * @var string
     */
    protected $_districtNameTable;

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
        $this->_init('directory_city_district', 'district_id');
        $this->_districtNameTable = $this->getTable('directory_city_district_name');
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

        $districtField = $connection->quoteIdentifier($this->getMainTable() . '.' . $this->getIdFieldName());

        $condition = $connection->quoteInto('lrn.locale = ?', $locale);
        $select->joinLeft(
            ['lrn' => $this->_districtNameTable],
            "{$districtField} = lrn.district_id AND {$condition}",
            []
        );

        if ($locale != $systemLocale) {
            $nameExpr = $connection->getCheckSql('lrn.district_id is null', 'srn.name', 'lrn.name');
            $condition = $connection->quoteInto('srn.locale = ?', $systemLocale);
            $select->joinLeft(
                ['srn' => $this->_districtNameTable],
                "{$districtField} = srn.district_id AND {$condition}",
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
     * @param int $cityId
     * @param string $value
     * @param string $field
     * @return $this
     */
    protected function _loadByDistrict($object, $cityId, $value, $field)
    {
        $connection = $this->getConnection();
        $locale = $this->_localeResolver->getLocale();
        $joinCondition = $connection->quoteInto('rname.district_id = region.district_id AND rname.locale = ?', $locale);
        $select = $connection->select()->from(
            ['district' => $this->getMainTable()]
        )->joinLeft(
            ['rname' => $this->_districtNameTable],
            $joinCondition,
            ['name']
        )->where(
            'district.city_id = ?',
            $cityId
        )->where(
            "district.{$field} = ?",
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
     * @param string $cityId
     * @return $this
     */
    public function loadByName(\Stableaddon\RegionalManagement\Model\SubDistrict $district, $districtName, $districtId)
    {
        return $this->_loadByDistrict($district, $districtId, (string)$districtName, 'default_name');
    }
}
