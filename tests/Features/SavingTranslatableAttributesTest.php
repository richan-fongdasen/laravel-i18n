<?php

namespace RichanFongdasen\I18n\Tests\Features;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use RichanFongdasen\I18n\Facade\I18n;
use RichanFongdasen\I18n\Tests\Supports\Models\Product;
use RichanFongdasen\I18n\Tests\TestCase;

class SavingTranslatableAttributesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_can_save_the_translation_by_specifying_translatable_attribute_values_one_by_one()
    {
        $product = Product::create([
            'product_category_id' => 3,
            'published' => true
        ]);
        $product->translateTo('en');
        $product->title = 'English title';
        $product->description = 'English description';

        $product->translateTo('es');
        $product->title = 'Spanish title';
        $product->description = 'Spanish description';

        $product->translateTo('de');
        $product->title = 'German title';
        $product->description = 'German description';

        $product->save();

        $product = Product::orderBy('id', 'desc')->first();

        foreach (I18n::getAllLocale() as $locale) {
            $product->translateTo($locale);

            self::assertEquals($locale->name . ' title', $product->title);
            self::assertEquals($locale->name . ' description', $product->description);
        }
    }

    /** @test */
    public function it_can_be_filled_and_saved_with_complete_translation_attributes()
    {
        $product = new Product();
        $product->fill([
            'product_category_id' => 3,
            'title' => [
                'en' => 'English title',
                'es' => 'Spanish title',
                'de' => 'German title',
            ],
            'description' => [
                'en' => 'English description',
                'es' => 'Spanish description',
                'de' => 'German description',
            ],
            'published' => true
        ])->save();

        $product = Product::orderBy('id', 'desc')->first();

        foreach (I18n::getAllLocale() as $locale) {
            $product->translateTo($locale);

            self::assertEquals($locale->name . ' title', $product->title);
            self::assertEquals($locale->name . ' description', $product->description);
        }
    }

    /** @test */
    public function it_can_be_filled_with_incomplete_translation_attributes()
    {
        $product = new Product();

        $product->fill([
            'product_category_id' => 3,
            'title' => [
                'en' => 'English title',
                'de' => 'German title',
            ],
            'description' => [
                'en' => 'English description',
                'es' => 'Spanish description',
            ],
            'published' => true
        ])->save();

        $product = Product::orderBy('id', 'desc')->first();

        $product->translateTo('en');
        self::assertEquals('English title', $product->title);
        self::assertEquals('English description', $product->description);

        $product->translateTo('es');
        self::assertEquals('English title', $product->title);
        self::assertEquals('Spanish description', $product->description);

        $product->translateTo('de');
        self::assertEquals('German title', $product->title);
        self::assertEquals('English description', $product->description);
    }

    /** @test */
    public function it_can_be_filled_with_only_default_translation_attributes()
    {
        $product = new Product();

        $product->fill([
            'product_category_id' => 3,
            'title' => 'English title',
            'description' => 'English description',
            'published' => true
        ])->save();

        $product = Product::orderBy('id', 'desc')->first();

        self::assertEquals('English title', $product->translation(I18n::getLocale('en'))->title);
        self::assertEquals('English description', $product->translation(I18n::getLocale('en'))->description);

        self::assertEquals(null, $product->translation(I18n::getLocale('es'))->title);
        self::assertEquals(null, $product->translation(I18n::getLocale('es'))->description);

        self::assertEquals(null, $product->translation(I18n::getLocale('de'))->title);
        self::assertEquals(null, $product->translation(I18n::getLocale('de'))->description);
    }

    /** @test */
    public function it_can_update_all_translation_records_with_mass_assignment()
    {
        $original = Product::create([
            'product_category_id' => 3,
            'title' => [
                'en' => 'English title',
                'es' => 'Spanish title',
            ],
            'description' => [
                'en' => 'English description',
                'es' => 'Spanish description',
            ],
            'published' => true
        ]);

        $product = Product::find($original->id);
        $product->fill([
            'title' => [
                'en' => 'English title 2',
                'es' => 'Spanish title 2',
                'de' => 'German title 2',
            ],
            'description' => [
                'en' => 'English description 2',
                'es' => 'Spanish description 2',
                'de' => 'German description 2',
            ],
        ])->save();
        $product = Product::find($original->id);

        foreach (I18n::getAllLocale() as $locale) {
            $product->translateTo($locale);

            self::assertEquals($locale->name . ' title 2', $product->title);
            self::assertEquals($locale->name . ' description 2', $product->description);
        }
    }

    /** @test */
    public function it_can_update_all_translation_records_with_single_assignment()
    {
        $original = new Product();
        $original->product_category_id = 3;
        // English value
        $original->title = 'English title';
        $original->description = 'English description';
        // Spanish value
        $original->translateTo('es');
        $original->title = 'Spanish title';
        $original->description = 'Spanish description';
        $original->save();

        $product = Product::find($original->id);
        // English value
        $product->title = 'English title 2';
        $product->description = 'English description 2';
        // Spanish value
        $product->translateTo('es');
        $product->title = 'Spanish title 2';
        $product->description = 'Spanish description 2';
        // German value
        $product->translateTo('de');
        $product->title = 'German title 2';
        $product->description = 'German description 2';
        $product->save();

        $product = Product::find($original->id);

        foreach (I18n::getAllLocale() as $locale) {
            $product->translateTo($locale);

            self::assertEquals($locale->name . ' title 2', $product->title);
            self::assertEquals($locale->name . ' description 2', $product->description);
        }
    }
}
