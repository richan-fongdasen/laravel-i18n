<?php

namespace RichanFongdasen\I18n\Eloquent\Concerns;

use ErrorException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use RichanFongdasen\I18n\Contracts\TranslatableModel;
use RichanFongdasen\I18n\Eloquent\I18nObserver;
use RichanFongdasen\I18n\Eloquent\TranslationModel;
use RichanFongdasen\I18n\Eloquent\TranslationScope;
use RichanFongdasen\I18n\Facade\I18n;
use RichanFongdasen\I18n\Locale;

/**
 * Translatable trait.
 *
 * @property string[] $translates
 * @property \Illuminate\Database\Eloquent\Collection $translations
 * @property string|null $translationTable
 */
trait Translatable
{
    /**
     * Currently active locale for a model.
     *
     * @var \RichanFongdasen\I18n\Locale
     */
    protected Locale $currentLocale;

    /**
     * Boot the Translatable trait model extension.
     *
     * @return void
     * @throws ErrorException
     */
    public static function bootTranslatable(): void
    {
        static::addGlobalScope(new TranslationScope());
        static::observe(app(I18nObserver::class));

        static::registerModelEvent('booted', static function (TranslatableModel $model) {
            $model->translate(App::getLocale());
        });
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param array $attributes
     * @return $this
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws ErrorException
     */
    public function fill(array $attributes)
    {
        foreach ($this->getTranslatableAttributes() as $columnName) {
            if (isset($attributes[$columnName])) {
                $this->setAttribute($columnName, $attributes[$columnName]);
            }
        }

        return parent::fill($attributes);
    }

    /**
     * Get all translatable attribute values in array.
     *
     * @return array
     * @throws ErrorException
     */
    public function getAllTranslationValues(): array
    {
        $result = [];
        $model = $this;
        $attributes = new Collection($this->getTranslatableAttributes());

        $attributes->each(function (string $attribute) use ($model, &$result) {
            if (!isset($result[$attribute])) {
                $result[$attribute] = [];
            }

            foreach (I18n::getAllLocale() as $locale) {
                $result[$attribute][$locale->getKey()] = $model->translation($locale)->getAttribute($attribute);
            }
        });

        return $result;
    }

    /**
     * Get an attribute from the model.
     *
     * @param string $key
     * @return mixed
     * @throws ErrorException
     */
    public function getAttribute($key)
    {
        if (in_array($key, $this->getTranslatableAttributes(), true)) {
            return $this->translation()->getAttribute($key) ?? $this->translation(I18n::getDefaultLocale())->getAttribute($key);
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

        foreach ($this->getTranslatableAttributes() as $attribute) {
            $attributes[] = $this->getTranslationTable().'.'.$attribute;
        }

        return $attributes;
    }

    /**
     * Get all of translatable attributes.
     *
     * @return string[]
     */
    public function getTranslatableAttributes(): array
    {
        return (isset($this->translates) && is_array($this->translates)) ? $this->translates : [];
    }

    /**
     * Get translation table.
     *
     * @return string
     */
    public function getTranslationTable(): string
    {
        return $this->translationTable ?? I18n::guessTranslationTable(class_basename($this));
    }

    /**
     * Add and additional scope to join the translation table
     * and make the translation content easier to search.
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
            ->where($this->getTranslationTable().'.locale', App::getLocale());
    }

    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return mixed
     * @throws ErrorException
     */
    public function setAttribute($key, $value)
    {
        if (!in_array($key, $this->getTranslatableAttributes(), true)) {
            return parent::setAttribute($key, $value);
        }

        if (!is_array($value)) {
            $this->translation()->setAttribute($key, $value);
        }

        if (is_array($value)) {
            foreach ($value as $localeKey => $val) {
                $locale = I18n::getLocale($localeKey);
                if (!($locale instanceof Locale)) {
                    continue;
                }

                $this->translation($locale)->setAttribute($key, $val);
            }
        }

        $this->updateTimestamps();

        return $this;
    }

    /**
     * Translate current model.
     *
     * @param Locale|string $locale
     *
     * @return $this
     * @throws ErrorException
     */
    public function translate($locale): self
    {
        if (is_string($locale)) {
            $locale = I18n::getLocale($locale);
        }

        if (!($locale instanceof Locale)) {
            throw new ErrorException('Failed to translate the model using the given locale.');
        }

        $this->currentLocale = $locale;

        return $this;
    }

    /**
     * Resolve and get the translation model for current locale.
     *
     * @param Locale|null $locale
     *
     * @return TranslationModel
     * @throws ErrorException
     */
    public function translation(?Locale $locale = null): TranslationModel
    {
        if ($locale === null) {
            $locale = $this->currentLocale;
        }

        $translation = $this->translations
            ->where('locale', $locale->getKey())
            ->first();

        if (!($translation instanceof TranslationModel)) {
            $translation = I18n::createTranslation($this, $locale);
            $this->translations->push($translation);
        }

        return $translation;
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