<?php

namespace RichanFongdasen\I18n\Eloquent;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use RichanFongdasen\I18n\Contracts\TranslatableModel;

class I18nObserver
{
    /**
     * Handle the TranslatableModel "saved" event.
     *
     * @param \RichanFongdasen\I18n\Contracts\TranslatableModel $model
     *
     * @throws \ErrorException
     *
     * @return void
     */
    public function retrieved(TranslatableModel $model): void
    {
        $model->translateTo(App::getLocale());
    }

    /**
     * Handle the TranslatableModel "saved" event.
     *
     * @param \RichanFongdasen\I18n\Contracts\TranslatableModel $model
     *
     * @return void
     */
    public function saved(TranslatableModel $model): void
    {
        if (!($model->getAttribute('translations') instanceof Collection)) {
            return;
        }

        $model->getAttribute('translations')->each(function (TranslationModel $translation) use ($model) {
            if ($translation->isDirty()) {
                $translation->setAttribute($model->getForeignKey(), $model->getKey())
                    ->setTable($model->getTranslationTable())
                    ->save();
            }
        });
    }
}
