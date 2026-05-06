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

use Mage_Core_Model_App_Area;
use Maho\ApiPlatform\Security\AdminSessionAuthenticator;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Bridges Maho's cookie-based admin session into the Symfony security context
 * for /api/admin/* endpoints by populating $_SERVER vars that
 * AdminSessionAuthenticator reads.
 *
 * Runs at high priority so the bridge is in place before the firewall fires.
 * Limited to /api/admin/* to keep the cost off public endpoints.
 */
#[AsEventListener(event: KernelEvents::REQUEST, priority: 100)]
class AdminBridgeListener
{
    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!str_contains($request->getPathInfo(), '/api/admin/')) {
            return;
        }

        \Mage::app()->loadAreaPart(
            Mage_Core_Model_App_Area::AREA_ADMINHTML,
            Mage_Core_Model_App_Area::PART_EVENTS,
        );

        $input = json_decode($request->getContent(), true) ?? [];
        $formKey = $input['form_key'] ?? null;

        $adminSession = \Mage::getSingleton('admin/session');
        $adminUser = null;

        if ($adminSession->isLoggedIn()) {
            $adminUser = $adminSession->getUser();
        } elseif ($formKey) {
            $sessionFormKey = \Mage::getSingleton('core/session')->getData('_form_key');
            $adminhtmlFormKey = \Mage::getSingleton('adminhtml/session')->getData('_form_key');
            if (($sessionFormKey === $formKey || $adminhtmlFormKey === $formKey) && $adminSession->getUser()) {
                $adminUser = $adminSession->getUser();
            }
        }

        if ($adminUser === null) {
            return;
        }

        $_SERVER['MAHO_ADMIN_USER_ID'] = $adminUser->getId();
        $_SERVER['MAHO_ADMIN_USERNAME'] = $adminUser->getUsername();
        $_SERVER['MAHO_STORE_ID'] = (int) ($input['variables']['storeId'] ?? 1);
        $_SERVER['MAHO_IS_ADMIN'] = '1';
        $_SERVER['MAHO_API_BRIDGE_TOKEN'] = AdminSessionAuthenticator::generateBridgeToken(
            (string) $adminUser->getId(),
        );
    }
}
