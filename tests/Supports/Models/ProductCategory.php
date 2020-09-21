<?php

namespace RichanFongdasen\I18n\Tests\Supports\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RichanFongdasen\I18n\Contracts\TranslateableModel;
use RichanFongdasen\I18n\Eloquent\Extensions\TranslateableTrait;

class ProductCategory extends Model implements TranslateableModel
{
    use HasFactory;
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
