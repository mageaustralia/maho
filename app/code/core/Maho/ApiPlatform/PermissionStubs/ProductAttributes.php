<?php

declare(strict_types=1);

/**
 * Maho
 *
 * @package    Maho_ApiPlatform
 * @copyright  Copyright (c) 2026 Maho (https://mahocommerce.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Maho\ApiPlatform\PermissionStubs;

use Maho\Config\ApiResource;

/**
 * Permission-only stub: surfaces the `product-attributes` resource in the
 * admin role editor without exposing any actual HTTP/GraphQL endpoints.
 * Delete this file when a real `Mage\Catalog\Api\ProductAttribute` DTO ships
 * — the maho fields will move there.
 *
 * `operations: []` (explicit empty, not null) tells API Platform "this resource
 * exists but has zero operations" — no routes registered, no provider/processor
 * needed. The maho fields drive the permission registry exactly like a regular DTO.
 */
#[ApiResource(
    mahoId: 'product-attributes',
    mahoSection: 'Catalog',
    mahoOperations: ['read' => 'View', 'write' => 'Create & Update', 'delete' => 'Delete'],
    operations: [],
)]
final class ProductAttributes {}
