<?php

namespace RichanFongdasen\I18n\Contracts;

use Illuminate\Database\Eloquent\Model;
use RichanFongdasen\I18n\Locale;

interface TranslatableModel
{
    /**
     * Fill the model with an array of attributes.
     *
     * @param array $attributes
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \ErrorException
     *
     * @return $this
     */
    public function fill(array $attributes);

    /**
     * Get all translatable attribute values in array.
     *
     * @throws \ErrorException
     *
     * @return array
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
     * @param mixed  $value
     *
     * @throws \ErrorException
     *
     * @return mixed
     */
    public function setAttribute($key, $value);

    /**
     * Translate current model.
     *
     * @param Locale|string $locale
     *
     * @throws \ErrorException
     *
     * @return $this
     */
    public function translateTo($locale): self;

    /**
     * Resolve and get the translation model for current locale.
     *
     * @param Locale|null $locale
     *
     * @throws \ErrorException
     *
     * @return Model
     */
    public function translation(?Locale $locale = null): Model;
}
