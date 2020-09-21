<?php

namespace RichanFongdasen\I18n\Tests;

use Faker\Generator;
use RichanFongdasen\I18n\Tests\Supports\Models\Product;
use RichanFongdasen\I18n\Tests\Supports\Models\ProductCategory;

abstract class DatabaseTestCase extends TestCase
{
    /**
     * Faker Generator Object
     *
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Default language key
     *
     * @var string
     */
    protected $key;

    /**
     * Locale collection
     *
     * @var \Illuminate\Support\Collection
     */
    protected $locale;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp() :void
    {
        parent::setUp();

        $this->prepareDatabase(
            realpath(__DIR__ . '/Supports/Migrations')
        );

        $this->faker = app(Generator::class);
        $this->key = \I18n::getConfig('language_key');
        $this->locale = \I18n::getLocale();

        $this->seedProductCategories();
        $this->seedProducts();
    }

    /**
     * Product Categories Database Seeder
     *
     * @return void
     */
    protected function seedProductCategories()
    {
        $faker = $this->faker;
        $key = $this->key;
        $locales = $this->locale;

        ProductCategory::factory(2)->create()
            ->each(function ($productCategory) use ($faker, $key, $locales) {
                foreach ($locales as $locale) {
                    \DB::table('product_category_translations')->insert([
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
    protected function seedProducts()
    {
        $categories = ProductCategory::get();

        $faker = $this->faker;
        $key = $this->key;
        $locales = $this->locale;

        foreach ($categories as $category) {
            Product::factory(6)->create([
                'product_category_id' => $category->id
            ])->each(function ($product) use ($faker, $key, $locales) {
                foreach ($locales as $locale) {
                    \DB::table('product_translations')->insert([
                        'product_id' => $product->id,
                        'locale' => $locale->{$key},
                        'title' => $faker->sentence(),
                        'description' => $faker->paragraph()
                    ]);
                }
            });
        }
    }

    /**
     * Listening to every database queries
     *
     * @return void
     */
    protected function listenForAnyQueries()
    {
        \DB::listen(function($query) {
            echo "\r\n" . $query->sql . "\r\n";
            return true;
        });
    }
}
