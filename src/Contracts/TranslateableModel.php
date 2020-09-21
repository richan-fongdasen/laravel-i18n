<?php

namespace RichanFongdasen\I18n\Contracts;

interface TranslateableModel
{
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
}
