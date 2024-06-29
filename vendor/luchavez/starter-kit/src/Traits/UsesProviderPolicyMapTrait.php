<?php

namespace Luchavez\StarterKit\Traits;

/**
 * Trait UsesProviderPolicyMapTrait
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
trait UsesProviderPolicyMapTrait
{
    /**
     * Laravel Policy Map
     *
     * @link    https://laravel.com/docs/8.x/authorization#registering-policies
     *
     * @example [ UserPolicy::class => User::class ]
     *
     * @var array
     */
    protected array $policy_map = [];

    /**
     * @return bool
     */
    public function arePoliciesEnabled(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function getPolicyMap(): array
    {
        return $this->policy_map;
    }

    /**
     * @param  array  $policy_map
     */
    public function setPolicyMap(array $policy_map): void
    {
        $this->policy_map = $policy_map;
    }

    /**
     * @param  array  $policy_map
     * @return $this
     */
    public function policyMap(array $policy_map): static
    {
        $this->setPolicyMap($policy_map);

        return $this;
    }
}
