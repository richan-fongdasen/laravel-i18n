<?php

namespace RichanFongdasen\I18n\Tests\Supports\Models;

use Illuminate\Database\Eloquent\Model;
use RichanFongdasen\I18n\Eloquent\Extensions\TranslateableTrait;
use RichanFongdasen\I18n\Tests\Supports\Models\Product;
use RichanFongdasen\I18n\Tests\Supports\Models\ProductCategoryTranslation;

class ProductCategory extends Model
{
    use TranslateableTrait;

    protected $translateFields = [
        'title',
        'description'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
