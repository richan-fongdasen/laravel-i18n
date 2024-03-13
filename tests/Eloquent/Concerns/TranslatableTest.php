<?php

namespace RichanFongdasen\I18n\Tests\Eloquent\Concerns;

use PHPUnit\Framework\Attributes\Test;
use RichanFongdasen\I18n\Facade\I18n;
use RichanFongdasen\I18n\Tests\Supports\Models\Product;
use RichanFongdasen\I18n\Tests\TestCase;

class TranslatableTest extends TestCase
{
    #[Test]
    public function it_can_create_new_translation_model_based_on_the_given_locale()
    {
        $product = new Product();
        $product->translateTo('es');

        $translation = $product->translation();

        $this->assertFalse($translation->exists);
        $this->assertEquals('es', $translation->locale);
    }

    #[Test]
    public function it_can_be_filled_with_complete_translation_attributes()
    {
        $product = new Product();

        $product->fill([
            'product_category_id' => 3,
            'title' => [
                'en' => 'English title',
                'es' => 'Spanish title',
                'de' => 'German title',
                'id' => 'Indonesian title',
            ],
            'description' => [
                'en' => 'English description',
                'es' => 'Spanish description',
                'de' => 'German description',
                'id' => 'Indonesian description',
            ],
            'published' => true
        ]);

        $this->assertEquals(3, $product->getAttribute('product_category_id'));
        $this->assertEquals(null, $product->getAttribute('published'));

        foreach (I18n::getAllLocale() as $locale) {
            $product->translateTo($locale);

            $this->assertEquals($locale->name . ' title', $product->title);
            $this->assertEquals($locale->name . ' description', $product->description);
        }

        $this->expectException(\ErrorException::class);
        $product->translateTo('id');
    }

    #[Test]
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

        $product->translateTo('es');
        $this->assertEquals('English title', $product->title);
        $this->assertEquals('Spanish description', $product->description);

        $product->translateTo('de');
        $this->assertEquals('German title', $product->title);
        $this->assertEquals('English description', $product->description);
    }

    #[Test]
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

    #[Test]
    public function it_returns_all_of_the_translatable_attributes()
    {
        $product = new Product();

        $expected = ['title', 'description'];
        $actual = $product->getTranslatableAttributes();

        $this->assertEquals($expected, $actual);
    }
}
