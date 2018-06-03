<?php

namespace RichanFongdasen\I18n\Tests\Supports\Models;

use Illuminate\Database\Eloquent\Model;
use RichanFongdasen\I18n\Eloquent\Extensions\TranslateableTrait;
use RichanFongdasen\I18n\Tests\Supports\Models\ProductCategory;
use RichanFongdasen\I18n\Tests\Supports\Models\ProductSpec;
use RichanFongdasen\I18n\Tests\Supports\Models\ProductTranslation;

class Product extends Model
{
    use TranslateableTrait;

    public $fillable = [
        'product_category_id',
    ];

    protected $translationTable = 'product_translations';

    protected $translateFields = [
        'title',
        'description'
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
}
