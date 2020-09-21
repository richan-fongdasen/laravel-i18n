<?php

namespace RichanFongdasen\I18n\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TranslationScope implements Scope
{
    /**
     * Eloquent model object.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $model;

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model   $model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $builder, Model $model): Builder
    {
        $this->model = $model;

        return $builder->with('translations');
    }
}
