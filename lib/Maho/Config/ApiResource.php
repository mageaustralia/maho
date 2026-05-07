<?php

declare(strict_types=1);

/**
 * Maho
 *
 * @package    Maho
 * @copyright  Copyright (c) 2026 Maho (https://mahocommerce.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Maho\Config;

use ApiPlatform\Metadata\ApiResource as BaseApiResource;
use ApiPlatform\Metadata\Parameters;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use ApiPlatform\State\OptionsInterface;
use Attribute;

/**
 * Maho-flavoured API Platform resource attribute.
 *
 * Drop-in subclass of `ApiPlatform\Metadata\ApiResource` that adds Maho's
 * permission-registry metadata directly to the same attribute. API Platform's
 * own scanner uses `is_a(..., true)` / `IS_INSTANCEOF` (see
 * `AttributesResourceNameCollectionFactory` and `MetadataCollectionFactoryTrait`),
 * so subclass instances are picked up exactly like the parent — HTTP routing
 * and GraphQL surface work unchanged.
 *
 * Compiled at `composer dump-autoload` into `vendor/composer/maho_api_permissions.php`,
 * consumed by `Maho\ApiPlatform\Security\ApiPermissionRegistry`. Run
 * `composer dump-autoload` after adding/modifying this attribute.
 *
 * Most fields default to `null` and are auto-derived by the compiler:
 *   - `mahoId`            ← shortName, pluralized + kebab-cased ('Cart' → 'carts')
 *   - `mahoLabel`         ← title-cased mahoId
 *   - `mahoSection`       ← module segment of the namespace ('Mage\Catalog\Api\…' → 'Catalog')
 *   - `mahoOperations`    ← derived from the parent `operations` array (presence of Get/Post/Put/Delete)
 *   - `mahoRestSegments`  ← unique first segments of each `uriTemplate`
 *   - `mahoGraphQlFields` ← every `name` from `graphQlOperations`
 *
 * Set them explicitly only when defaults are wrong. `mahoPublicRead`,
 * `mahoCustomerScoped`, and `mahoDescription` have no API Platform equivalent
 * and must be set explicitly when needed.
 *
 * For forward-looking resources without a real DTO, declare on a stub class
 * with `operations: []` (explicit empty — *not* null) so API Platform sees
 * the resource but registers zero endpoints; only the maho fields are picked up.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class ApiResource extends BaseApiResource
{
    /**
     * Mirrors `ApiPlatform\Metadata\ApiResource::__construct` parameter-by-parameter
     * and forwards them to `parent::__construct` so API Platform sees the same
     * configuration. Maho-specific fields come *after* every parent parameter so
     * positional usage (rare) still maps cleanly to the parent contract.
     *
     * If API Platform adds a new constructor parameter we haven't mirrored here,
     * `tests/Backend/Integration/ApiPlatform/ApiResourceConstructorParityTest`
     * fails — keeping the mirror honest.
     *
     * The `@param` overrides for `$operations` and `$rules` exist to detach our
     * signature from the parent docblock — the parent annotates `$operations`
     * with a generic Operations type (which would require a `@template` tag we
     * don't carry) and `$rules` with `Illuminate\Contracts\Validation\Rule`
     * (Laravel — not a Maho dependency). Both are pure pass-through values; we
     * accept them as `mixed` and forward verbatim.
     *
     * @param mixed                      $operations
     * @param mixed                      $rules
     * @param array<string, string>|null $mahoOperations  e.g. ['read' => 'View', 'write' => 'Manage']
     * @param string[]|null              $mahoRestSegments
     * @param string[]|null              $mahoGraphQlFields
     */
    public function __construct(
        // ---- Maho permission-registry fields (named-arg first; positional usage of the
        //      parent ApiResource ctor is impractical given its 70+ params and never
        //      seen in real code, so leading with the maho fields here just makes them
        //      surface first in IDE autocomplete and at the top of usage blocks).
        public ?string $mahoId = null,
        public ?string $mahoLabel = null,
        public ?string $mahoGroup = null,
        public ?string $mahoSection = null,
        public ?array $mahoOperations = null,
        public bool $mahoPublicRead = false,
        public bool $mahoCustomerScoped = false,
        public ?array $mahoRestSegments = null,
        public ?array $mahoGraphQlFields = null,
        public ?string $mahoDescription = null,
        // ---- Mirror of ApiPlatform\Metadata\ApiResource constructor ----
        ?string $uriTemplate = null,
        ?string $shortName = null,
        ?string $description = null,
        string|array|null $types = null,
        $operations = null,
        array|string|null $formats = null,
        array|string|null $inputFormats = null,
        array|string|null $outputFormats = null,
        $uriVariables = null,
        ?string $routePrefix = null,
        ?array $defaults = null,
        ?array $requirements = null,
        ?array $options = null,
        ?bool $stateless = null,
        ?string $sunset = null,
        ?string $acceptPatch = null,
        ?int $status = null,
        ?string $host = null,
        ?array $schemes = null,
        ?string $condition = null,
        ?string $controller = null,
        ?string $class = null,
        ?int $urlGenerationStrategy = null,
        ?string $deprecationReason = null,
        ?array $headers = null,
        ?array $cacheHeaders = null,
        ?array $normalizationContext = null,
        ?array $denormalizationContext = null,
        ?bool $collectDenormalizationErrors = null,
        ?array $hydraContext = null,
        bool|OpenApiOperation|null $openapi = null,
        ?array $validationContext = null,
        ?array $filters = null,
        $mercure = null,
        $messenger = null,
        $input = null,
        $output = null,
        ?array $order = null,
        ?bool $fetchPartial = null,
        ?bool $forceEager = null,
        ?bool $paginationClientEnabled = null,
        ?bool $paginationClientItemsPerPage = null,
        ?bool $paginationClientPartial = null,
        ?array $paginationViaCursor = null,
        ?bool $paginationEnabled = null,
        ?bool $paginationFetchJoinCollection = null,
        ?bool $paginationUseOutputWalkers = null,
        ?int $paginationItemsPerPage = null,
        ?int $paginationMaximumItemsPerPage = null,
        ?bool $paginationPartial = null,
        ?string $paginationType = null,
        string|\Stringable|null $security = null,
        ?string $securityMessage = null,
        string|\Stringable|null $securityPostDenormalize = null,
        ?string $securityPostDenormalizeMessage = null,
        string|\Stringable|null $securityPostValidation = null,
        ?string $securityPostValidationMessage = null,
        ?bool $compositeIdentifier = null,
        ?array $exceptionToStatus = null,
        ?bool $queryParameterValidationEnabled = null,
        ?array $links = null,
        ?array $graphQlOperations = null,
        $provider = null,
        $processor = null,
        ?OptionsInterface $stateOptions = null,
        mixed $rules = null,
        ?string $policy = null,
        array|string|null $middleware = null,
        array|Parameters|null $parameters = null,
        ?bool $strictQueryParameterValidation = null,
        ?bool $hideHydraOperation = null,
        ?bool $jsonStream = null,
        array $extraProperties = [],
        ?bool $map = null,
        ?array $mcp = null,
    ) {
        // Forward every locally-defined non-maho parameter to the parent
        // constructor. Using compact() keeps the call site automatically in
        // sync with the parameter list above — adding/removing a parent arg
        // only requires editing the signature, not the forward call.
        $localVars = get_defined_vars();
        $parentArgs = [];
        foreach ($localVars as $name => $value) {
            if (!str_starts_with($name, 'maho')) {
                $parentArgs[$name] = $value;
            }
        }
        parent::__construct(...$parentArgs);
    }
}
