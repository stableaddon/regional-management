<?php

namespace Stableaddon\RegionalManagement\Setup;

use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 *
 * @package Stableaddon\RegionalManagement\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Framework\App\Config\ConfigResource\ConfigInterface
     */
    protected $resourceConfig;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $installer;

    /**
     * Customer setup factory
     *
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    public function __construct(
        ConfigInterface $resourceConfig,
        ResourceConnection $setup,
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
    )
    {
        $this->resourceConfig = $resourceConfig;
        $this->installer = $setup;
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order_address'),
            'sub_district_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'comment' => 'Sub District ID'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('quote_address'),
            'sub_district_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'comment' => 'Sub District ID'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order_address'),
            'city_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'comment' => 'City ID'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('quote_address'),
            'city_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'comment' => 'City ID'
            ]
        );

        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerSetup->addAttribute('customer_address', 'city_id', [
            'type' => 'int',
            'label' => 'District',
            'input' => 'select',
            'source' => \Stableaddon\RegionalManagement\Model\ResourceModel\Source\City::class,
            'required' => false,
            'sort_order' => 105,
            'position' => 105
        ]);


        $customerSetup->addAttribute('customer_address', 'sub_district', [
            'label' => 'Sub District',
            'input' => 'text',
            'type' => 'static',
            'source' => '',
            'required' => false,
            'position' => 106,
            'visible' => true,
            'system' => false,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => false,
            'backend' => ''
        ]);

        $customerSetup->addAttribute('customer_address', 'sub_district_id', [
            'type' => 'int',
            'label' => 'Sub District',
            'input' => 'select',
            'source' => \Stableaddon\RegionalManagement\Model\ResourceModel\Source\SubDistrict::class,
            'required' => false,
            'sort_order' => 106,
            'position' => 106
        ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'sub_district')
            ->addData(['used_in_forms' => [
                'customer_address_edit',
                'customer_register_address',
                'adminhtml_customer_address'
            ]]);
        $attribute->save();

        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'sub_district_id')
            ->addData(['used_in_forms' => [
                'customer_register_address',
                'customer_address_edit',
                'adminhtml_customer_address'
            ]]);
        $attribute->save();

        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'city_id')
            ->addData(['used_in_forms' => [
                'customer_register_address',
                'customer_address_edit',
                'adminhtml_customer_address'
            ]]);
        $attribute->save();

        $customerSetup->updateAttribute('customer_address', 'city', 'sort_order', 105);

        $setup->endSetup();
    }
}
