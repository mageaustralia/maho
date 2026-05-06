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
 * GraphQL endpoint shim
 *
 * Maho's standard router parses URLs as /{frontName}/{controller}/{action}, so the
 * canonical Symfony URL "/api/graphql" lands here as controller=graphql, action=index.
 * This shim forwards to IndexController::indexAction so the Symfony kernel boots and
 * dispatches the GraphQL route.
 */
class Maho_ApiPlatform_GraphqlController extends Maho_ApiPlatform_IndexController
{
    #[\Override]
    public function indexAction(): void
    {
        parent::indexAction();
    }
}
