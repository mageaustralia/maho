<?php

declare(strict_types=1);

/**
 * Maho
 *
 * @package    Maho_Catalog
 * @copyright  Copyright (c) 2026 Maho (https://mahocommerce.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Maho\Catalog\Api\Resource;

/**
 * Visual swatch data for a filter option
 */
class FilterOptionSwatch
{
    /**
     * @param string $type  Swatch type: "color" (hex value) or "image" (URL)
     * @param string $value Hex color (e.g. "#FF0000") or full image URL
     */
    public function __construct(
        public string $type = 'color',
        public string $value = '',
    ) {}
}
