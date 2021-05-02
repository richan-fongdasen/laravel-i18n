<?php

namespace RichanFongdasen\I18n\Tests\Supports\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RichanFongdasen\I18n\Contracts\TranslatableModel;
use RichanFongdasen\I18n\Eloquent\Extensions\Translatable;

class ProductCategory extends Model implements TranslatableModel
{
    use HasFactory;
    use Translatable;

    protected $translateFields = [
        'title',
        'description'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
