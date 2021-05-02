<?php

namespace RichanFongdasen\I18n\Eloquent\Extensions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use RichanFongdasen\I18n\Eloquent\Observer;
use RichanFongdasen\I18n\Eloquent\TranslationModel;
use RichanFongdasen\I18n\Eloquent\TranslationScope;
use RichanFongdasen\I18n\Locale;

trait TranslateableTrait
{
    /**
     * Fallback translation object. The model will use
     * the value from this object, if there was no
     * translated attribute value for current locale.
     *
     * @var \RichanFongdasen\I18n\Eloquent\TranslationModel|null
     */
    protected ?TranslationModel $fallbackTranslation;

    /**
     * Current selected locale.
     *
     * @var \RichanFongdasen\I18n\Locale
     */
    protected Locale $locale;

    /**
     * Default language key.
     *
     * @var string
     */
    protected static string $localeKey;

    /**
     * Translation object for the current selected
     * locale.
     *
     * @var \RichanFongdasen\I18n\Eloquent\TranslationModel|null
     */
    protected ?TranslationModel $translation;

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    public function attributesToArray(): array
    {
        $attributes = parent::attributesToArray();

        foreach ($this->getTranslateableAttributes() as $key) {
            $attributes[$key] = $this->getAttribute($key);
        }

        return $attributes;
    }

    /**
     * Boot the TranslateableTrait model extension.
     *
     * @return void
     */
    public static function bootTranslateableTrait(): void
    {
        static::addGlobalScope(new TranslationScope());
        static::observe(app(Observer::class));
        static::$localeKey = \I18n::getConfig('language_key');
    }

    /**
     * Create a new translation for the given locale.
     *
     * @param \RichanFongdasen\I18n\Locale|null $locale
     *
     * @return \RichanFongdasen\I18n\Eloquent\TranslationModel
     */
    protected function createTranslation(?Locale $locale): TranslationModel
    {
        if ($locale === null) {
            $locale = \I18n::defaultLocale();
        }

        $model = (new TranslationModel())
            ->setTable($this->getTranslationTable())
            ->fill([
                $this->getForeignKey() => $this->getKey(),
                'locale'               => $locale->{self::$localeKey},
            ]);

        $this->translations->push($model);

        return $model;
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function fill(array $attributes): self
    {
        foreach ($this->getTranslateableAttributes() as $key) {
            if (isset($attributes[$key])) {
                $this->setAttribute($key, $attributes[$key]);
            }
        }

        return parent::fill($attributes);
    }

    /**
     * Get an attribute from the model.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        if ($this->isTranslateableAttribute($key)) {
            return $this->getTranslated($key);
        }

        return parent::getAttribute($key);
    }

    /**
     * Get join table attributes.
     *
     * @return string[]
     */
    protected function getJoinAttributes(): array
    {
        $attributes = [$this->getTable().'.*'];

        foreach ($this->getTranslateableAttributes() as $attribute) {
            $attributes[] = $this->getTranslationTable().'.'.$attribute;
        }

        return $attributes;
    }

    /**
     * Get all of translateable attributes.
     *
     * @return array
     */
    public function getTranslateableAttributes(): array
    {
        return is_array($this->translateFields) ? $this->translateFields : [];
    }

    /**
     * Get a translated attribute value from
     * the model.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function getTranslated(string $key)
    {
        $this->initialize();

        $translation = $this->getTranslation($this->locale);

        return data_get($translation, $key) ?? data_get($this->fallbackTranslation, $key);
    }

    /**
     * Get existing translation or create a
     * new one.
     *
     * @param \RichanFongdasen\I18n\Locale $locale
     *
     * @return \RichanFongdasen\I18n\Eloquent\TranslationModel
     */
    protected function getTranslation(?Locale $locale = null): TranslationModel
    {
        $this->translate($locale);

        if (!($this->translation instanceof TranslationModel)) {
            $this->translation = $this->createTranslation($locale);
        }

        return $this->translation;
    }

    /**
     * Find locale object based on the given
     * key value.
     *
     * @param mixed $key
     *
     * @return \RichanFongdasen\I18n\Locale
     */
    protected function getTranslationLocale($key = null): Locale
    {
        if ($key instanceof Locale) {
            return $key;
        }

        if (($key === null) || empty($key)) {
            $key = \App::getLocale();
        }

        if (!$locale = \I18n::getLocale($key)) {
            $locale = \I18n::defaultLocale();
        }

        return $locale;
    }

    /**
     * Get translation table.
     *
     * @return string
     */
    public function getTranslationTable(): string
    {
        if (!isset($this->translationTable)) {
            $suffix = \I18n::getConfig('translation_table_suffix');

            return Str::snake(class_basename($this)).'_'.$suffix;
        }

        return $this->translationTable;
    }

    /**
     * Initialize translateable features by setting up required properties.
     */
    protected function initialize(): void
    {
        if (!isset($this->locale)) {
            $this->locale = $this->getTranslationLocale();
        }

        if (!isset($this->fallbackTranslation)) {
            $locale = \I18n::defaultLocale()->{self::$localeKey};
            $this->fallbackTranslation = $this->translations->where('locale', $locale)->first();
        }
    }

    /**
     * Check whether the given attribute key is
     * translateable.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function isTranslateableAttribute(string $key): bool
    {
        $fields = $this->getTranslateableAttributes();

        return in_array($key, $fields, true);
    }

    /**
     * Add and additional scope to join the translation table
     * and make the translation content more easier to search.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeJoinTranslation(Builder $query): Builder
    {
        $attributes = $this->getJoinAttributes();

        return $query->leftJoin(
            $this->getTranslationTable(),
            $this->getTable().'.'.$this->getKeyName(),
            '=',
            $this->getTranslationTable().'.'.$this->getForeignKey()
        )->select($attributes)
        ->where($this->getTranslationTable().'.locale', \App::getLocale());
    }

    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if ($this->isTranslateableAttribute($key)) {
            $this->initialize();

            if (is_array($value)) {
                foreach ($value as $locale => $val) {
                    $translation = $this->getTranslation($this->getTranslationLocale($locale));
                    $translation->setAttribute($key, $val);
                }
            }

            if (!is_array($value)) {
                $translation = $this->getTranslation($this->locale);
                $translation->setAttribute($key, $value);
            }

            $this->updateTimestamps();

            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Translate current model.
     *
     * @param mixed $key
     *
     * @return $this
     */
    public function translate($key = null): self
    {
        $this->initialize();

        $this->locale = $this->getTranslationLocale($key);

        $key = $this->locale->{self::$localeKey};
        $this->translation = $this->translations->where('locale', $key)->first();

        return $this;
    }

    /**
     * Define HasMany model relationship
     * with its translation model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations(): HasMany
    {
        $model = new TranslationModel();
        $model->setTable($this->getTranslationTable());

        return new HasMany(
            $model->newQuery(),
            $this,
            $this->getForeignKey(),
            $this->getKeyName()
        );
    }

    /**
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    abstract public function getForeignKey();

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    abstract public function getKey();

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    abstract public function getKeyName();

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    abstract public function getTable();

    /**
     * Update the creation and update timestamps.
     *
     * @return void
     */
    abstract protected function updateTimestamps();
}
