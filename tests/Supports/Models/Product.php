<?php

namespace RichanFongdasen\I18n\Tests\Supports\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RichanFongdasen\I18n\Contracts\TranslateableModel;
use RichanFongdasen\I18n\Eloquent\Extensions\TranslateableTrait;

class Product extends Model implements TranslateableModel
{
    use HasFactory;
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
