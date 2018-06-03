<?php

namespace RichanFongdasen\I18n\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Observer
{
    /**
     * Check if the translation model is dirty
     *
     * @param  \RichanFongdasen\I18n\Eloquent\TranslationModel $model
     * @return boolean
     */
    protected function isDirty(TranslationModel $model)
    {
        return $model->isDirty();
    }

    /**
     * Listening to any saved events.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function saved(Model $model)
    {
        foreach ($model->translations as $translation) {
            if ($this->isDirty($translation)) {
                $translation->setAttribute($model->getForeignKey(), $model->getKey())
                    ->setTable($model->getTranslationTable())
                    ->save();
            }
        }
    }
}
