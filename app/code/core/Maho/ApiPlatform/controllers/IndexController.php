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
 * Legacy SOAP/XML-RPC/JSON-RPC dispatcher.
 *
 * Modern /api/* requests are rewritten to rest.php (Symfony API Platform). The
 * .htaccess RewriteCond at public/.htaccess:76 carves out the four legacy paths
 * below so they fall through to index.php, where these #[Route] attributes match
 * and forward to the original Mage_Api_*Controller classes.
 */
class Maho_ApiPlatform_IndexController extends Mage_Core_Controller_Front_Action
{
    #[Maho\Config\Route('/api/soap', methods: ['GET', 'POST'])]
    public function soapAction(): void
    {
        $this->forwardToLegacy(Mage_Api_SoapController::class);
    }

    #[Maho\Config\Route('/api/v2_soap', methods: ['GET', 'POST'])]
    public function v2SoapAction(): void
    {
        $this->forwardToLegacy(Mage_Api_V2_SoapController::class);
    }

    #[Maho\Config\Route('/api/xmlrpc', methods: ['GET', 'POST'])]
    public function xmlrpcAction(): void
    {
        $this->forwardToLegacy(Mage_Api_XmlrpcController::class);
    }

    #[Maho\Config\Route('/api/jsonrpc', methods: ['GET', 'POST'])]
    public function jsonrpcAction(): void
    {
        $this->forwardToLegacy(Mage_Api_JsonrpcController::class);
    }

    private function forwardToLegacy(string $controllerClass): void
    {
        $controller = new $controllerClass($this->getRequest(), $this->getResponse());
        $controller->dispatch('index');
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
    }
}
