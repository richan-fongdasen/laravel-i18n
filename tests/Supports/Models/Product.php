<?php

namespace RichanFongdasen\I18n\Tests\Supports\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RichanFongdasen\I18n\Contracts\TranslatableModel;
use RichanFongdasen\I18n\Eloquent\Extensions\Translatable;

class Product extends Model implements TranslatableModel
{
    use HasFactory;
    use Translatable;

    public $fillable = [
        'product_category_id',
    ];

    protected $translationTable = 'product_translations';

    protected $translateFields = [
        'title',
        'description'
    ];

    public function getTitleAttribute(): string
    {
        return (string) $this->getAttribute('title');
    }

    public function getDescriptionAttribute(): string
    {
        return (string) $this->getAttribute('description');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
}
