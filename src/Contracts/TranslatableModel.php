<?php

namespace RichanFongdasen\I18n\Contracts;

use RichanFongdasen\I18n\Eloquent\TranslationModel;
use RichanFongdasen\I18n\Locale;

interface TranslatableModel
{
    /**
     * Fill the model with an array of attributes.
     *
     * @param array $attributes
     * @return $this
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \ErrorException
     */
    public function fill(array $attributes);

    /**
     * Get all translatable attribute values in array.
     *
     * @return array
     * @throws \ErrorException
     */
    public function getAllTranslationValues(): array;

    /**
     * Get an attribute from the model.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute($key);

    /**
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey();

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey();

    /**
     * Get all of translatable attributes.
     *
     * @return string[]
     */
    public function getTranslatableAttributes(): array;

    /**
     * Get translation table.
     *
     * @return string
     */
    public function getTranslationTable(): string;

    /**
     * Determine if the model or any of the given attribute(s) have been modified.
     *
     * @param array|string|null $attributes
     *
     * @return bool
     */
    public function isDirty($attributes = null);

    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function setAttribute($key, $value);

    /**
     * Translate current model.
     *
     * @param Locale|string $locale
     *
     * @return $this
     * @throws \ErrorException
     */
    public function translate($locale): self;

    /**
     * Resolve and get the translation model for current locale.
     *
     * @param Locale|null $locale
     *
     * @return TranslationModel
     * @throws \ErrorException
     */
    public function translation(?Locale $locale = null): TranslationModel;
}
