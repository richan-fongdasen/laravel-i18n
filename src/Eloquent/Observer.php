<?php

namespace RichanFongdasen\I18n\Eloquent;

use RichanFongdasen\I18n\Contracts\TranslatableModel;

class Observer
{
    /**
     * Check if the translation model is dirty.
     *
     * @param \RichanFongdasen\I18n\Eloquent\TranslationModel $model
     *
     * @return bool
     */
    protected function isDirty(TranslationModel $model): bool
    {
        return $model->isDirty();
    }

    /**
     * Listening to any saved events.
     *
     * @param \RichanFongdasen\I18n\Contracts\TranslatableModel $model
     *
     * @return void
     */
    public function saved(TranslatableModel $model): void
    {
        foreach ($model->getAttribute('translations') as $translation) {
            if ($this->isDirty($translation)) {
                $translation->setAttribute($model->getForeignKey(), $model->getKey())
                    ->setTable($model->getTranslationTable())
                    ->save();
            }
        }
    }
}
