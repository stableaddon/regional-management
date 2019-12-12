<?php

namespace Stableaddon\RegionalManagement\Setup;

/**
 * Class InstallSchema
 *
 * @package Stableaddon\RegionalManagement\Setup
 */
class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * install tables
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $setup->getConnection()->addColumn(
            $setup->getTable('quote_address'),
            'sub_district',
            [
                'type' => 'text',
                'length' => 255,
                'comment' => 'Sub District'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order_address'),
            'sub_district',
            [
                'type' => 'text',
                'length' => 255,
                'comment' => 'Sub District'
            ]
        );

        $table = $installer->getConnection()->newTable(
            $installer->getTable('directory_region_city')
        )->addColumn(
            'city_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
                'auto_increment' => true
            ],
            'City Id'
        )->addColumn(
            'region_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Region Id'
        )->addColumn(
                'default_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'default' => NULL
                ],
                'City Name'
        )->addIndex(
            $setup->getIdxName('directory_region_city', array('region_id')),
            'region_id'
        )->addForeignKey(
            $setup->getFkName('directory_region_city','region_id','directory_country_region','region_id'),
            'region_id',
            'directory_country_region',
                    'region_id'
        )->setComment(
            'Directory Region City'
        );

        $installer->getConnection()->createTable($table);

        $table = $setup->getConnection()->newTable(
            $setup->getTable('directory_region_city_name')
        )->addColumn(
            'locale',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            8,
            [
                'nullable' => false
            ],
            'Locale'
        )->addColumn(
            'city_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'City Id'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [
                'default' => 'NULL'
            ],
            'City Name'
        )->addIndex(
            $setup->getIdxName('directory_region_city_name', array('locale', 'city_id')),
            array('locale', 'city_id')
        )->addForeignKey(
            $setup->getFkName(
                'directory_region_city_name',
                'city_id',
                'directory_region_city',
                'city_id'
            ),
            'city_id',
            $setup->getTable('directory_region_city'),
            'city_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Directory Region City Name'
        );

        $setup->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('directory_city_district')
        )->addColumn(
            'district_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
                'auto_increment' => true
            ],
            'District Id'
        )->addColumn(
            'city_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'City Id'
        )->addColumn(
            'default_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [
                'default' => NULL
            ],
            'District Name'
        )->addColumn(
            'postcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [
                'default' => NULL
            ],
            'Postcode'
        )->addIndex(
            $setup->getIdxName('directory_city_district', array('city_id')),
            'city_id'
        )->addForeignKey(
            $setup->getFkName('directory_city_district','city_id','directory_region_city','city_id'),
            'city_id',
            'directory_region_city',
            'city_id'
        )->setComment(
            'Directory City District'
        );

        $installer->getConnection()->createTable($table);

        $table = $setup->getConnection()->newTable(
            $setup->getTable('directory_city_district_name')
        )->addColumn(
            'locale',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            8,
            [
                'nullable' => false
            ],
            'Locale'
        )->addColumn(
            'district_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'District Id'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [
                'default' => 'NULL'
            ],
            'District Name'
        )->addIndex(
            $setup->getIdxName('directory_city_district_name', array('locale', 'district_id')),
            array('locale', 'district_id')
        )->addForeignKey(
            $setup->getFkName(
                'directory_city_district_name',
                'district_id',
                'directory_city_district',
                'district_id'
            ),
            'district_id',
            $setup->getTable('directory_city_district'),
            'district_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Directory City District Name'
        );
        $setup->getConnection()->createTable($table);

        $setup->getConnection()->addColumn(
            $setup->getTable('customer_address_entity'),
            'sub_district_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'comment' => 'Sub District ID'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('customer_address_entity'),
            'sub_district',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'comment' => 'Sub District'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('customer_address_entity'),
            'city_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'comment' => 'Sub city_id'
            ]
        );

        $installer->endSetup();
    }
}
