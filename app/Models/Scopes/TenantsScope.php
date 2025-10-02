<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantsScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole()) {
            return; // Jangan apply scope saat jalan di CLI
        }

        $user = Auth::user();

        if ($user && $user->role !== 'super_admin') {
            $builder->where($model->getTable() . '.tenant_id', $user->tenant_id);
        }
    }

}