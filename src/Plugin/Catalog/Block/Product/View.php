<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionStore\Plugin\Catalog\Block\Product;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class View
{
    public function afterHasOptions(\Magento\Catalog\Block\Product\View $subject, bool $result): bool
    {
        if ($result) {
            $result = count($subject->getProduct()->getOptions()) > 0;
        }

        return $result;
    }
}
