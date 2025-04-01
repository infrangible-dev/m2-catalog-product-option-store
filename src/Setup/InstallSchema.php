<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionStore\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @throws \Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        $enabledTableName = $setup->getTable('catalog_product_option_enabled');

        if (! $setup->tableExists($enabledTableName)) {
            $optionTableName = $setup->getTable('catalog_product_option');
            $storeTableName = $setup->getTable('store');

            $enabledTable = $connection->newTable($enabledTableName);

            $enabledTable->addColumn(
                'id',
                Table::TYPE_INTEGER,
                10,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            );
            $enabledTable->addColumn(
                'option_id',
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false]
            );
            $enabledTable->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false]
            );
            $enabledTable->addColumn(
                'enabled',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false]
            );

            $enabledTable->addForeignKey(
                $setup->getFkName(
                    $enabledTableName,
                    'option_id',
                    $optionTableName,
                    'option_id'
                ),
                'option_id',
                $optionTableName,
                'option_id',
                Table::ACTION_CASCADE
            );

            $enabledTable->addForeignKey(
                $setup->getFkName(
                    $enabledTableName,
                    'store_id',
                    $storeTableName,
                    'store_id'
                ),
                'store_id',
                $storeTableName,
                'store_id',
                Table::ACTION_CASCADE
            );

            $connection->createTable($enabledTable);
        }

        $setup->endSetup();
    }
}
