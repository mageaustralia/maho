<?php

declare(strict_types=1);

/**
 * Maho
 *
 * @package    Maho_Ai
 * @copyright  Copyright (c) 2026 Maho (https://mahocommerce.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Maho_Ai_Model_Source_Models
{
    /** Provider being filtered to. Empty string = all providers. */
    protected string $provider = '';

    public function toOptionArray(): array
    {
        return $this->getForProvider($this->provider);
    }

    /**
     * Return the cached model list for the provider, or a placeholder option
     * if the admin hasn't clicked "Update Models" yet. Any currently-saved
     * config value is preserved as an option so saving the form doesn't blank
     * it out when the cache is empty.
     */
    protected function getForProvider(string $provider): array
    {
        if ($provider === '') {
            return [];
        }

        $cached = Mage::getStoreConfig("maho_ai/models_cache/{$provider}");
        if ($cached) {
            try {
                $decoded = Mage::helper('core')->jsonDecode($cached);
                if (is_array($decoded) && $decoded !== []) {
                    return $decoded;
                }
            } catch (\JsonException) {
                // fall through to placeholder
            }
        }

        $current = (string) Mage::getStoreConfig("maho_ai/general/{$provider}_model");
        $options = [];
        if ($current !== '') {
            $options[] = ['value' => $current, 'label' => $current];
        }
        $options[] = [
            'value' => '',
            'label' => Mage::helper('ai')->__('— click "Update Models" to load available models —'),
        ];
        return $options;
    }
}
