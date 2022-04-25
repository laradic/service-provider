<?php

namespace Laradic\ServiceProvider\Concerns;

use Illuminate\Support\Facades\Gate;

/**
 * @mixin \Laradic\ServiceProvider\BaseServiceProvider
 */
trait Policies
{
    public $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    private function initPoliciesTrait()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
    }

}
