<?php

namespace RichanFongdasen\I18n\Tests\Supports\Concerns;

use Faker\Generator;
use Illuminate\Support\Facades\DB;
use RichanFongdasen\I18n\Facade\I18n;
use RichanFongdasen\I18n\Tests\Supports\Models\Product;
use RichanFongdasen\I18n\Tests\Supports\Models\ProductCategory;

trait SeedsRequiredDatabase
{
    /**
     * Seed the required database.
     *
     * @return void
     */
    protected function seedDatabase(): void
    {
        $this->seedProductCategories();
        $this->seedProducts();
    }

    /**
     * Product Categories Database Seeder
     *
     * @return void
     */
    private function seedProductCategories(): void
    {
        $faker = app(Generator::class);
        $key = config('i18n.language_key');
        $locales = I18n::getAllLocale();

        ProductCategory::factory(2)->create()
            ->each(function ($productCategory) use ($faker, $key, $locales) {
                foreach ($locales as $locale) {
                    DB::table('product_category_translations')->insert([
                        'product_category_id' => $productCategory->id,
                        'locale' => $locale->{$key},
                        'title' => $faker->sentence(),
                        'description' => $faker->paragraph()
                    ]);
                }
            });
    }

    /**
     * Products Database Seeder
     *
     * @return void
     */
    private function seedProducts(): void
    {
        $categories = ProductCategory::get();

        $faker = app(Generator::class);
        $key = config('i18n.language_key');
        $locales = I18n::getAllLocale();

        foreach ($categories as $category) {
            Product::factory(6)->create([
                'product_category_id' => $category->id
            ])->each(function ($product) use ($faker, $key, $locales) {
                foreach ($locales as $locale) {
                    DB::table('product_translations')->insert([
                        'product_id' => $product->id,
                        'locale' => $locale->{$key},
                        'title' => $faker->sentence(),
                        'description' => $faker->paragraph()
                    ]);
                }
            });
        }
    }
}
