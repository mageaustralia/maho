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
 * Permission-only stub for the `payments` resource.
 * See `ProductAttributes` for the pattern.
 */
#[ApiResource(
    mahoId: 'payments',
    mahoSection: 'Sales',
    mahoOperations: ['read' => 'View', 'write' => 'Record'],

    operations: [],
)]
final class Payments {}
