<?php

namespace RichanFongdasen\I18n\Tests\Supports\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RichanFongdasen\I18n\Contracts\TranslatableModel;
use RichanFongdasen\I18n\Eloquent\Concerns\Translatable;

/**
 * Product model.
 *
 * @property string $title
 * @property string $description
 */
class Product extends Model implements TranslatableModel
{
    use HasFactory;
    use Translatable;

    public $fillable = [
        'product_category_id',
    ];

    protected string $translationTable = 'product_translations';

    protected array $translates = [
        'title',
        'description'
    ];

    protected $hidden = ['translations'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function getTitleAttribute(): string
    {
        return (string) $this->getAttribute('title');
    }

    public function getDescriptionAttribute(): string
    {
        return (string) $this->getAttribute('description');
    }
}
