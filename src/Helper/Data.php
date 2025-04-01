<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionStore\Helper;

use Magento\Catalog\Model\ResourceModel\Product\Option\Collection;
use Magento\Store\Model\Store;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Data
{
    public function addEnabledToResult(Collection $collection, $storeId): void
    {
        if ($collection->isLoaded() || $collection->hasFlag('enabled')) {
            return;
        }

        $dbAdapter = $collection->getConnection();

        $tableName = $dbAdapter->getTableName('catalog_product_option_enabled');

        $enabledExpr = $dbAdapter->getCheckSql(
            'store_option_enabled.enabled IS NULL',
            'default_option_enabled.enabled',
            'store_option_enabled.enabled'
        );

        $select = $collection->getSelect();

        $select->joinLeft(
            ['default_option_enabled' => $tableName],
            sprintf(
                'default_option_enabled.option_id = main_table.option_id AND %s',
                $dbAdapter->quoteInto(
                    'default_option_enabled.store_id = ?',
                    Store::DEFAULT_STORE_ID
                )
            ),
            ['default_enabled' => 'enabled']
        );

        $select->joinLeft(
            ['store_option_enabled' => $tableName],
            sprintf(
                'store_option_enabled.option_id = main_table.option_id AND %s',
                $dbAdapter->quoteInto(
                    'store_option_enabled.store_id = ?',
                    $storeId
                )
            ),
            ['store_enabled' => 'enabled', 'enabled' => $enabledExpr]
        );

        $collection->setFlag(
            'enabled',
            true
        );
    }

    public function addEnabledFilterToResult(Collection $collection, $storeId): void
    {
        if ($collection->isLoaded() || $collection->hasFlag('enabled_filter')) {
            return;
        }

        $this->addEnabledToResult(
            $collection,
            $storeId
        );

        $dbAdapter = $collection->getConnection();

        $enabledExpr = $dbAdapter->getCheckSql(
            'store_option_enabled.enabled IS NULL',
            'default_option_enabled.enabled',
            'store_option_enabled.enabled'
        );

        $select = $collection->getSelect();

        $select->where(
            sprintf(
                '(%s = ?) OR (%s IS NULL)',
                $enabledExpr,
                $enabledExpr
            ),
            1
        );

        $collection->setFlag(
            'enabled_filter',
            true
        );
    }
}
