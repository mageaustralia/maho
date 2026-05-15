<?php

declare(strict_types=1);

/**
 * Maho
 *
 * @package    Maho_Ai
 * @copyright  Copyright (c) 2026 Maho (https://mahocommerce.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Maho_Ai_Block_Adminhtml_System_Config_Form_Field_ModelSelect extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    #[\Override]
    protected function _getElementHtml(\Maho\Data\Form\Element\AbstractElement $element): string
    {
        $html = parent::_getElementHtml($element);

        // Extract provider and capability group from element HTML ID: maho_ai_{group}_{provider}_model
        $elementId = $element->getHtmlId();
        $provider = null;
        $capability = 'chat';
        if (preg_match('/^maho_ai_(general|image|embed|video)_(.+)_model$/', $elementId, $matches)) {
            $group = $matches[1];
            $candidate = $matches[2];
            // Check if provider has a model fetcher (built-in method or community class)
            $config = Maho_Ai_Model_Platform::getProviderConfig($candidate);
            if ($config && ((string) ($config->model_fetcher_method ?? '') || (string) ($config->model_fetcher_class ?? ''))) {
                $provider = $candidate;
                $capability = $group === 'general' ? 'chat' : $group;
            }
        }

        if ($provider === null) {
            return $html;
        }

        $url      = $this->getUrl('*/ai/fetchModels', ['provider' => $provider, 'capability' => $capability]);
        $label    = Mage::helper('ai')->__('Update Models');
        $fetching = Mage::helper('ai')->__('Fetching...');
        $errorPfx = Mage::helper('ai')->__('Error: ');
        $btnId    = 'maho-ai-fetch-' . $elementId;

        $btn = sprintf(
            '<button type="button" id="%s"><span>%s</span></button>',
            $this->escapeHtml($btnId),
            $this->escapeHtml($label),
        );

        $btnIdJs    = json_encode($btnId);
        $urlJs      = json_encode($url);
        $targetJs   = json_encode($elementId);
        $fetchingJs = json_encode($fetching);
        $errorPfxJs = json_encode($errorPfx);

        $script = <<<HTML
<script>
mahoOnReady(function () {
    const btn = document.getElementById({$btnIdJs});
    if (!btn) return;

    btn.addEventListener('click', async function () {
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = {$fetchingJs};

        try {
            const data = await mahoFetch({$urlJs}, { method: 'POST', loaderArea: false });
            if (data.error) {
                alert({$errorPfxJs} + data.error);
                return;
            }

            let el = document.getElementById({$targetJs});
            if (!el) return;
            const current = el.value;

            if (el.tagName === 'INPUT') {
                const sel = document.createElement('select');
                sel.id = el.id;
                sel.name = el.name;
                sel.className = el.className;
                sel.style.cssText = el.style.cssText;
                el.parentNode.replaceChild(sel, el);
                el = sel;
            }

            el.innerHTML = '';
            (data.models || []).forEach(function (m) {
                const opt = document.createElement('option');
                opt.value = m.value;
                opt.text = m.label;
                if (m.value === current) opt.selected = true;
                el.appendChild(opt);
            });
            if (current && !el.querySelector('option[selected]')) {
                el.value = current;
            }
        } catch (err) {
            alert({$errorPfxJs} + (err && err.message ? err.message : err));
        } finally {
            btn.disabled = false;
            btn.innerHTML = orig;
        }
    });
});
</script>
HTML;

        return '<div class="ai-model-select-row">' . $html . $btn . $script . '</div>';
    }
}
