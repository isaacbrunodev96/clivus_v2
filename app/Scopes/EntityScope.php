<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class EntityScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Do not apply in console context
        if (app()->runningInConsole()) {
            return;
        }

        $selectedType = session('selected_entity_type', null);
        $selectedCompany = session('selected_company_id', null);

        // If nothing selected, do not filter
        if (!$selectedType) {
            return;
        }

        // If model has company_id column, we can filter directly; otherwise use account relation fallback
        $hasCompanyColumn = \Illuminate\Support\Facades\Schema::hasColumn($model->getTable(), 'company_id');

        if ($selectedType === 'cnpj') {
            if ($selectedCompany) {
                if ($hasCompanyColumn) {
                    $builder->where($model->getTable() . '.company_id', $selectedCompany);
                } else {
                    // fallback: filter by related account.company_id if relation exists
                    if (method_exists($model, 'account')) {
                        $builder->where(function($q) use ($selectedCompany) {
                            $q->whereHas('account', function($aq) use ($selectedCompany) {
                                $aq->where('company_id', $selectedCompany);
                            });
                        });
                    }
                }
            } else {
                // no company selected: show nothing for cnpj context
                $builder->whereRaw('1 = 0');
            }
        } else {
            // CPF selected: exclude company-associated records
            if ($hasCompanyColumn) {
                $builder->whereNull($model->getTable() . '.company_id');
            } else {
                // fallback: ensure related account has no company if relation exists
                if (method_exists($model, 'account')) {
                    $builder->where(function($q) {
                        $q->whereDoesntHave('account', function($aq) {
                            $aq->whereNotNull('company_id');
                        });
                    });
                }
            }
        }
    }
}

