<?php

namespace RichanFongdasen\I18n\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ScopeInterface;

class TranslationScope implements ScopeInterface
{
    public $model;

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model   $model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $builder, Model $model)
    {
        $this->model = $model;

        return $builder->with('translations');
    }

    /**
     * We don't intend to remove this global scope, but
     * we need to implement this method regarding to
     * \Illuminate\Database\Eloquent\ScopeInterface.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model   $model
     *
     * @return void
     */
    public function remove(Builder $builder, Model $model)
    {
        $this->model = $model;

        return $builder;
    }
}
