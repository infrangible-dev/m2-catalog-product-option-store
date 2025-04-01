<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionStore\Observer;

use FeWeDev\Base\Arrays;
use Infrangible\Core\Helper\Database;
use Magento\Catalog\Model\Product\Option;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class ModelSaveAfter implements ObserverInterface
{
    /** @var Database */
    protected $databaseHelper;

    /** @var Arrays */
    protected $arrays;

    public function __construct(Database $databaseHelper, Arrays $arrays)
    {
        $this->databaseHelper = $databaseHelper;
        $this->arrays = $arrays;
    }

    /**
     * @throws \Exception
     */
    public function execute(Observer $observer): void
    {
        $object = $observer->getData('object');

        if ($object instanceof Option) {
            $optionId = $object->getData('option_id');
            $storeId = $object->getData('store_id');
            $enabled = $object->getData('enabled');

            $dbAdapter = $object->getResource()->getConnection();

            $tableName = $dbAdapter->getTableName('catalog_product_option_enabled');

            $query = $this->databaseHelper->select(
                $tableName,
                ['id', 'enabled']
            );

            $query->where(
                'option_id = ?',
                $optionId
            );

            $query->where(
                'store_id  = ?',
                $storeId
            );

            $queryResult = $this->databaseHelper->fetchRow(
                $query,
                $dbAdapter
            );

            if ($queryResult === null) {
                $this->databaseHelper->createTableData(
                    $dbAdapter,
                    $tableName,
                    ['option_id' => $optionId, 'store_id' => $storeId, 'enabled' => $enabled]
                );
            } else {
                $currentValue = $this->arrays->getValue(
                    $queryResult,
                    'enabled'
                );

                if ($currentValue != $enabled) {
                    $id = $this->arrays->getValue(
                        $queryResult,
                        'id'
                    );

                    $this->databaseHelper->updateTableData(
                        $dbAdapter,
                        $tableName,
                        ['enabled' => $enabled],
                        sprintf(
                            'id = %d',
                            $id
                        )
                    );
                }
            }
        }
    }
}
