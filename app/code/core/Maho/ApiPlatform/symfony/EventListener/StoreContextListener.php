<?php

declare(strict_types=1);

/**
 * Maho
 *
 * @package    Maho_ApiPlatform
 * @copyright  Copyright (c) 2026 Maho (https://mahocommerce.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Maho\ApiPlatform\EventListener;

use Mage_Core_Model_Store_Exception;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Honors the `store` query parameter or `X-Store-Code` header by pointing
 * Maho's app at the matching store before any controller or authenticator runs.
 *
 * Priority is set above the security firewall so providers/processors that
 * resolve store-scoped models see the right store from the start.
 */
#[AsEventListener(event: KernelEvents::REQUEST, priority: 100)]
class StoreContextListener
{
    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $storeCode = $request->query->get('store') ?? $request->headers->get('X-Store-Code');
        if (!$storeCode) {
            return;
        }

        try {
            $store = \Mage::app()->getStore($storeCode);
            if ($store && $store->getId()) {
                \Mage::app()->setCurrentStore($store);
            }
        } catch (Mage_Core_Model_Store_Exception) {
            // Invalid store code — fall back to default
        }
    }
}
