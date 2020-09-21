<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use RichanFongdasen\I18n\Tests\Supports\Models\ProductCategory;

class ProductCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [];
    }
}
