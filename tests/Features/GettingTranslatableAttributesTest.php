<?php

namespace RichanFongdasen\I18n\Tests\Features;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use RichanFongdasen\I18n\Facade\I18n;
use RichanFongdasen\I18n\Tests\Supports\Concerns\SeedsRequiredDatabase;
use RichanFongdasen\I18n\Tests\Supports\Models\Product;
use RichanFongdasen\I18n\Tests\TestCase;

class GettingTranslatableAttributesTest extends TestCase
{
    use DatabaseMigrations;
    use SeedsRequiredDatabase;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seedDatabase();
    }

    /** @test */
    public function it_returns_translation_attribute_correctly()
    {
        App::setLocale('es');

        $product = Product::find(5);

        $expected = DB::table('product_translations')
            ->where('product_id', 5)
            ->where('locale', 'es')
            ->first();

        self::assertEquals($expected->title, $product->title);
        self::assertEquals($expected->description, $product->description);
    }

    /** @test */
    public function it_returns_translation_value_correctly()
    {
        App::setLocale('de');

        $product = Product::find(5);

        $expected = DB::table('product_translations')
            ->where('product_id', 5)
            ->where('locale', 'de')
            ->first();

        self::assertEquals($expected->title, $product->translation()->title);
        self::assertEquals($expected->description, $product->translation()->description);
    }

    /** @test */
    public function it_returns_fallback_value_when_the_translated_value_is_not_available()
    {
        App::setLocale('es');

        $statement = "DELETE FROM `product_translations` WHERE `product_id`=5 AND `locale`='es'";
        DB::delete($statement);

        $product = Product::find(5);

        $expected = DB::table('product_translations')
            ->where('product_id', 5)
            ->where('locale', I18n::getDefaultLocale()->getKey())
            ->first();

        self::assertEquals($expected->title, $product->title);
        self::assertEquals($expected->description, $product->description);
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
        self::assertEquals($expected, Product::joinTranslation()->getQuery()->columns);
    }

    /** @test */
    public function it_can_translate_the_model_based_on_the_given_locale_key()
    {
        $product = Product::find(9)->translateTo('de');

        $expected = DB::table('product_translations')
            ->where('product_id', 9)
            ->where('locale', 'de')
            ->first();

        self::assertEquals($expected->title, $product->title);
        self::assertEquals($expected->description, $product->description);
    }

    /** @test */
    public function it_can_retrieve_all_translatable_attribute_values()
    {
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
        $expected = [
            'title' => [
                'en' => 'English title',
                'es' => 'Spanish title',
                'de' => null,
            ],
            'description' => [
                'en' => 'English description',
                'es' => 'Spanish description',
                'de' => null,
            ],
        ];

        self::assertEquals($expected, $product->getAllTranslationValues());
    }

    /** @test */
    public function it_will_merge_the_translation_attributes_on_array_serialization()
    {
        $product = Product::find(8)
            ->translateTo('es')
            ->setAppends(['title', 'description']);

        $translation = \DB::table('product_translations')
            ->where('product_id', 8)
            ->where('locale', 'es')
            ->first();

        $expected = [
            'id'                  => $product->getKey(),
            'product_category_id' => $product->product_category_id,
            'created_at'          => $product->created_at->toJson(),
            'updated_at'          => $product->updated_at->toJson(),
            'title'               => $translation->title,
            'description'         => $translation->description,
        ];

        self::assertEquals($expected, $product->toArray());
    }

    /** @test */
    public function eloquent_collections_are_translatable()
    {
        $products = Product::where('product_category_id', 1)->get();

        $products->translateTo('de');

        foreach ($products as $product) {
            $translation = \DB::table('product_translations')
                ->where('product_id', $product->getKey())
                ->where('locale', 'de')
                ->first();

            $this->assertEquals($translation->title, $product->title);
            $this->assertEquals($translation->description, $product->description);
        }
    }
}
