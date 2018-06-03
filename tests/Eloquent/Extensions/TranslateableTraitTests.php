<?php

namespace RichanFongdasen\I18n\Tests\Eloquent\Extensions;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use RichanFongdasen\I18n\Eloquent\TranslationModel;
use RichanFongdasen\I18n\Locale;
use RichanFongdasen\I18n\Tests\DatabaseTestCase;
use RichanFongdasen\I18n\Tests\Supports\Models\Product;
use RichanFongdasen\I18n\Tests\Supports\Models\ProductCategory;

class TranslateableTraitTests extends DatabaseTestCase
{
    /** @test */
    public function it_will_merge_the_translation_attributes_on_array_serialization()
    {
        $product = Product::find(8)->translate('es')->toArray();

        $expected = \DB::table('product_translations')
            ->where('product_id', 8)
            ->where('locale', 'es')
            ->first();

        $this->assertEquals($expected->title, $product['title']);
        $this->assertEquals($expected->description, $product['description']);
    }

    /** @test */
    public function it_can_create_new_translation_model_based_on_the_given_locale()
    {
        $locale = \I18n::getLocale('es');
        $product = new Product();

        $translation = $this->invokeMethod($product, 'createTranslation', [$locale]);

        $this->assertInstanceOf(TranslationModel::class, $translation);
        $this->assertFalse($translation->exists);
        $this->assertEquals('es', $translation->locale);
    }

    /** @test */
    public function it_can_be_filled_with_complete_translation_attributes()
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
        ]);

        $this->assertEquals(3, $product->getAttribute('product_category_id'));
        $this->assertEquals(null, $product->getAttribute('published'));

        foreach (\I18n::getLocale() as $locale) {
            $product->translate($locale);

            $this->assertEquals($locale->name . ' title', $product->title);
            $this->assertEquals($locale->name . ' description', $product->description);
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
        ]);

        $product->translate('es');
        $this->assertEquals('English title', $product->title);
        $this->assertEquals('Spanish description', $product->description);

        $product->translate('de');
        $this->assertEquals('German title', $product->title);
        $this->assertEquals('English description', $product->description);
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
        ]);

        $locale = \I18n::getLocale('en');
        $translation = $this->invokeMethod($product, 'getTranslation', [$locale]);
        $this->assertEquals('English title', $translation->title);
        $this->assertEquals('English description', $translation->description);

        $locale = \I18n::getLocale('es');
        $translation = $this->invokeMethod($product, 'getTranslation', [$locale]);
        $this->assertEquals(null, $translation->title);
        $this->assertEquals(null, $translation->description);

        $locale = \I18n::getLocale('de');
        $translation = $this->invokeMethod($product, 'getTranslation', [$locale]);
        $this->assertEquals(null, $translation->title);
        $this->assertEquals(null, $translation->description);
    }

    /** @test */
    public function it_returns_parent_model_attribute_correctly()
    {
        $product = Product::find(5);
        $categoryId = $product->getAttribute('product_category_id');
        $productId = $product->getAttribute('id');

        $this->assertEquals(1, $categoryId);
        $this->assertEquals(5, $productId);
    }

    /** @test */
    public function it_returns_translation_attribute_correctly()
    {
        \App::setLocale('es');

        $product = Product::find(5);
        $actual = $product->description;

        $expected = \DB::table('product_translations')
            ->where('product_id', 5)
            ->where('locale', 'es')
            ->first()->description;

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_can_generate_join_attributes_correctly()
    {
        $product = new Product();

        $expected = [
            'products.*',
            'product_translations.title',
            'product_translations.description'
        ];
        $actual = $this->invokeMethod($product, 'getJoinAttributes');

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_returns_all_of_the_translateable_attributes()
    {
        $product = new Product();

        $expected = ['title', 'description'];
        $actual = $this->invokeMethod($product, 'getTranslateableAttributes');

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_returns_translation_value_correctly()
    {
        \App::setLocale('de');

        $product = Product::find(5);
        $actual = $this->invokeMethod($product, 'getTranslated', ['title']);

        $expected = \DB::table('product_translations')
            ->where('product_id', 5)
            ->where('locale', 'de')
            ->first()->title;

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_returns_fallback_value_when_the_translated_value_is_not_available()
    {
        \App::setLocale('es');

        $statement = "DELETE FROM `product_translations` WHERE `product_id`=5 AND `locale`='es'";
        \DB::delete($statement);

        $product = Product::find(5);
        $actual = $this->invokeMethod($product, 'getTranslated', ['title']);

        $expected = \DB::table('product_translations')
            ->where('product_id', 5)
            ->where('locale', \I18n::defaultLocale()->language)
            ->first()->title;

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_returns_null_if_the_given_translation_is_not_a_model_object()
    {
        $product = new Product();
        $locale = \I18n::defaultLocale();

        $actual = $this->invokeMethod($product, 'getTranslatedValue', [$locale, 'title']);

        $this->assertEquals(null, $actual);
    }

    /** @test */
    public function it_returns_translated_value_based_on_the_given_translation_model()
    {
        $product = new Product();
        $model = new TranslationModel();
        $model->setTable('product_translations');
        $translation = $model->where('product_id', 5)
            ->where('locale', \I18n::defaultLocale()->language)
            ->first();

        $actual = $this->invokeMethod($product, 'getTranslatedValue', [$translation, 'title']);

        $this->assertEquals($translation->title, $actual);
    }

    /** @test */
    public function it_can_retrieve_existing_translation_object_correctly()
    {
        $locale = \I18n::getLocale('es');
        $product = Product::create([
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
        $translation = $this->invokeMethod($product, 'getTranslation', [$locale]);

        $this->assertInstanceOf(TranslationModel::class, $translation);
        $this->assertTrue($translation->exists);
        $this->assertEquals('Spanish title', $translation->title);
        $this->assertEquals('Spanish description', $translation->description);
    }

    /** @test */
    public function it_returns_new_translation_object_on_undefined_locale()
    {
        $locale = new Locale('China', 'cn', 'CN');
        $product = new Product();

        $translation = $this->invokeMethod($product, 'getTranslation', [$locale]);
        $this->assertInstanceOf(TranslationModel::class, $translation);
        $this->assertFalse($translation->exists);
    }

    /** @test */
    public function it_returns_translation_locale_based_on_the_given_locale_object()
    {
        $locale = new Locale('China', 'cn', 'CN');
        $product = new Product();

        $actual = $this->invokeMethod($product, 'getTranslationLocale', [$locale]);

        $this->assertEquals($locale, $actual);
    }

    /** @test */
    public function it_returns_translation_locale_based_on_the_given_locale_string()
    {
        $product = new Product();

        $actual = $this->invokeMethod($product, 'getTranslationLocale', ['es']);

        $this->assertEquals('es-ES', $actual->ietfCode);
    }

    /** @test */
    public function it_returns_translation_locale_based_on_fallback_locale()
    {
        $product = new Product();

        $actual = $this->invokeMethod($product, 'getTranslationLocale', ['ar']);

        $this->assertEquals('en-US', $actual->ietfCode);
    }

    /** @test */
    public function it_returns_the_translation_table_correctly()
    {
        $product = new Product();

        $expected = 'product_translations';
        $actual = $product->getTranslationTable();

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_generate_table_name_automatically_when_translation_table_is_undefined()
    {
        $productCategory = new ProductCategory();
        $actual = $productCategory->getTranslationTable();

        $this->assertEquals('product_category_translations', $actual);
    }

    /** @test */
    public function it_can_identify_translateable_attributes_correctly()
    {
        $product = new Product();

        $this->assertTrue($this->invokeMethod($product, 'isTranslateableAttribute', ['title']));
        $this->assertTrue($this->invokeMethod($product, 'isTranslateableAttribute', ['description']));
        $this->assertFalse($this->invokeMethod($product, 'isTranslateableAttribute', ['price']));
    }

    /** @test */
    public function it_can_join_the_translation_table()
    {
        $join = data_get(Product::joinTranslation()->getQuery()->joins, '0');

        $this->assertInstanceOf(JoinClause::class, $join);
        $this->assertEquals('left', $join->type);
        $this->assertEquals('product_translations', $join->table);

        $expected = [
            'products.*',
            'product_translations.title',
            'product_translations.description'
        ];
        $this->assertEquals($expected, Product::joinTranslation()->getQuery()->columns);
    }

    /** @test */
    public function it_can_set_translateable_attributes_using_magic_set_method()
    {
        $product = new Product();
        $product->title = 'English title';
        $product->description = 'English description';

        $product->translate('es');
        $product->title = 'Spanish title';
        $product->description = 'Spanish description';

        $product->translate('de');
        $product->title = 'German title';
        $product->description = 'German description';

        foreach (\I18n::getLocale() as $locale) {
            $translation = $this->invokeMethod($product, 'getTranslation', [$locale]);

            $this->assertEquals($locale->name . ' title', $translation->title);
            $this->assertEquals($locale->name . ' description', $translation->description);
        }
    }

    /** @test */
    public function it_can_determine_the_fallback_translation_correctly()
    {
        $locale = \I18n::defaultLocale();
        $product = Product::find(7)->translate('es');

        $expected = \DB::table('product_translations')
            ->where('product_id', 7)
            ->where('locale', $locale->language)
            ->first();

        $actual = $this->getPropertyValue($product, 'fallbackTranslation');

        $this->assertEquals($expected->id, $actual->id);
        $this->assertEquals($expected->title, $actual->title);
        $this->assertEquals($expected->description, $actual->description);
    }

    /** @test */
    public function it_can_set_single_translateable_attribute_value_with_defined_locale()
    {
        $product = new Product();
        $this->invokeMethod($product, 'setTranslateableAttribute', ['title', 'Spanish title', 'es']);

        $locale = \I18n::getLocale('es');
        $translation = $this->invokeMethod($product, 'getTranslation', [$locale]);
        $this->assertEquals('Spanish title', $translation->title);
        $this->assertEquals(null, $translation->description);
    }

    /** @test */
    public function it_can_set_single_translateable_attribute_value_with_undefined_locale()
    {
        $product = new Product();
        $this->invokeMethod($product, 'setTranslateableAttribute', ['title', 'English title']);
        $product->translate('de');
        $this->invokeMethod($product, 'setTranslateableAttribute', ['title', 'German title']);

        $locale = \I18n::getLocale('en');
        $translation = $this->invokeMethod($product, 'getTranslation', [$locale]);
        $this->assertEquals('English title', $translation->title);
        $this->assertEquals(null, $translation->description);

        $locale = \I18n::getLocale('de');
        $translation = $this->invokeMethod($product, 'getTranslation', [$locale]);
        $this->assertEquals('German title', $translation->title);
        $this->assertEquals(null, $translation->description);
    }

    /** @test */
    public function it_can_set_multiple_translateable_attribute_value_with_multiple_languages()
    {
        $product = new Product();
        $data = [
            'en' => 'English title',
            'es' => 'Spanish title',
            'de' => 'German title',
        ];
        $this->invokeMethod($product, 'setTranslateableAttribute', ['title', $data]);
        
        $locale = \I18n::getLocale('en');
        $translation = $this->invokeMethod($product, 'getTranslation', [$locale]);
        $this->assertEquals('English title', $translation->title);
        $this->assertEquals(null, $translation->description);

        $locale = \I18n::getLocale('es');
        $translation = $this->invokeMethod($product, 'getTranslation', [$locale]);
        $this->assertEquals('Spanish title', $translation->title);
        $this->assertEquals(null, $translation->description);

        $locale = \I18n::getLocale('de');
        $translation = $this->invokeMethod($product, 'getTranslation', [$locale]);
        $this->assertEquals('German title', $translation->title);
        $this->assertEquals(null, $translation->description);
    }

    /** @test */
    public function it_can_translate_the_model_based_on_the_given_locale_key()
    {
        $product = Product::find(9)->translate('de');

        $expected = \DB::table('product_translations')
            ->where('product_id', 9)
            ->where('locale', 'de')
            ->first();

        $actual = $this->getPropertyValue($product, 'translation');

        $this->assertEquals($expected->id, $actual->id);
        $this->assertEquals($expected->title, $actual->title);
        $this->assertEquals($expected->description, $actual->description);
    }

    /** @test */
    public function the_has_many_relation_is_up_and_running()
    {
        $product = Product::find(9);

        $expected = \DB::table('product_translations')
            ->where('product_id', 9)
            ->get();

        $actual = $product->translations;

        if ($expected instanceof Collection) {
            $count = $expected->count();
        } else {
            $count = count($expected);
        }

        for ($i = 0; $i < $count; $i++) {
            $this->assertEquals($expected[$i]->id, $actual[$i]->id);
            $this->assertEquals($expected[$i]->title, $actual[$i]->title);
            $this->assertEquals($expected[$i]->description, $actual[$i]->description);
        }
    }

    /** @test */
    public function it_can_save_all_translation_records_as_expected()
    {
        $original = Product::create([
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
        ]);

        $product = Product::find($original->id);

        foreach (\I18n::getLocale() as $locale) {
            $product->translate($locale);

            $this->assertEquals($locale->name . ' title', $product->title);
            $this->assertEquals($locale->name . ' description', $product->description);
        }
    }

    /** @test */
    public function it_can_save_incomplete_translation_records_and_retrieve_them_as_expected()
    {
        $original = Product::create([
            'product_category_id' => 3,
            'title' => [
                'en' => 'English title',
                'es' => 'Spanish title',
            ],
            'description' => [
                'en' => 'English description',
                'de' => 'German description',
            ],
            'published' => true
        ]);

        $product = Product::find($original->id);

        $product->translate('es');
        $this->assertEquals('Spanish title', $product->title);
        $this->assertEquals('English description', $product->description);

        $product->translate('de');
        $this->assertEquals('English title', $product->title);
        $this->assertEquals('German description', $product->description);
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

        foreach (\I18n::getLocale() as $locale) {
            $product->translate($locale);

            $this->assertEquals($locale->name . ' title 2', $product->title);
            $this->assertEquals($locale->name . ' description 2', $product->description);
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
        $original->translate('es');
        $original->title = 'Spanish title';
        $original->description = 'Spanish description';
        $original->save();

        $product = Product::find($original->id);
        // English value
        $product->title = 'English title 2';
        $product->description = 'English description 2';
        // Spanish value
        $product->translate('es');
        $product->title = 'Spanish title 2';
        $product->description = 'Spanish description 2';
        // German value
        $product->translate('de');
        $product->title = 'German title 2';
        $product->description = 'German description 2';
        $product->save();

        $product = Product::find($original->id);

        foreach (\I18n::getLocale() as $locale) {
            $product->translate($locale);

            $this->assertEquals($locale->name . ' title 2', $product->title);
            $this->assertEquals($locale->name . ' description 2', $product->description);
        }
    }
}
