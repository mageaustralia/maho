<?php

declare(strict_types=1);

/**
 * Maho
 *
 * @package    Maho_ApiPlatform
 * @copyright  Copyright (c) 2026 Maho (https://mahocommerce.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Maho\ApiPlatform\Security;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Static helper for inline admin-ACL checks.
 *
 * Mirrors `Mage_Adminhtml_Controller_Action::_isAllowed()`. Call from any
 * request-time code (handler methods, action methods, processors) that needs
 * to gate work behind the admin's Maho ACL:
 *
 *     AdminAcl::checkResource(\Mage\Sales\Api\Order::class);
 *
 * The check reads the resource class's ADMIN_RESOURCE constant and calls
 * Mage::getSingleton('admin/session')->isAllowed() against it. The check is
 * a no-op if the current request isn't authenticated as an admin (so customer
 * and API-user tokens fall through to their own permission systems).
 *
 * Why static and not a Symfony service: this is meant to be a one-line
 * inline gate that any module's handler can drop in, with no constructor
 * plumbing. Modules don't need to register anything centrally — they just
 * import the resource class they're acting on and call this helper.
 */
final class AdminAcl
{
    /**
     * Throw 403 if the current request is an admin token whose ACL doesn't
     * permit the resource class's ADMIN_RESOURCE.
     *
     * @param class-string $resourceClass A class declaring `public const ADMIN_RESOURCE = '…';`
     */
    public static function checkResource(string $resourceClass): void
    {
        $session = \Mage::getSingleton('admin/session');
        if (!$session->getUser()) {
            // Not an admin context — let other gates handle this request.
            return;
        }

        try {
            $aclPath = (new \ReflectionClass($resourceClass))->getConstant('ADMIN_RESOURCE');
        } catch (\ReflectionException) {
            $aclPath = null;
        }

        if (!is_string($aclPath) || $aclPath === '') {
            // Default-deny: the resource didn't declare ADMIN_RESOURCE, so
            // it isn't an admin-callable surface. Same policy as
            // AdminAclListener for the REST surface.
            throw new AccessDeniedHttpException(
                sprintf('%s declares no ADMIN_RESOURCE constant.', $resourceClass),
            );
        }

        if (!$session->isAllowed($aclPath)) {
            throw new AccessDeniedHttpException(
                sprintf('Your admin role does not grant access to "%s".', $aclPath),
            );
        }
    }
}
