<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionStore\Plugin\Catalog\Model\Product\Option;

use Magento\Catalog\Model\Product\Option;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Repository
{
    /**
     * @noinspection PhpUnusedParameterInspection
     */
    public function afterGetProductOptions(
        Option\Repository $subject,
        array $options
    ): array {
        /** @var Option $option */
        foreach ($options as $key => $option) {
            if ($option->getData('enabled') !== null && $option->getData('enabled') == 0) {
                unset($options[ $key ]);
            }
        }

        return $options;
    }
}
