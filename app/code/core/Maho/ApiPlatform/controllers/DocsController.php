<?php

declare(strict_types=1);

/**
 * Maho
 *
 * @package    Maho_ApiPlatform
 * @copyright  Copyright (c) 2026 Maho (https://mahocommerce.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API docs endpoint shim
 *
 * Maps "/api/docs" to the Symfony kernel via IndexController::indexAction.
 * See GraphqlController for the rationale.
 */
class Maho_ApiPlatform_DocsController extends Maho_ApiPlatform_IndexController
{
    #[\Override]
    public function indexAction(): void
    {
        parent::indexAction();
    }
}
