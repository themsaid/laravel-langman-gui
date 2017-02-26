<?php

namespace Themsaid\LangmanGUI;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class LangmanController
{
    /**
     * Return view for index screen.
     *
     * @return Response
     */
    public function index()
    {
        return view('langmanGUI::admin', [
            'translations' => app(Manager::class)->getTranslations(),
            'languages' => array_keys(app(Manager::class)->getTranslations())
        ]);
    }

    /**
     * Synchronize with the project files and find untranslated keys.
     *
     * @return Response
     */
    public function sync()
    {
        return response(app(Manager::class)->sync());
    }

    /**
     * Save the translations
     *
     * @return void
     */
    public function save()
    {
        app(Manager::class)->saveTranslations(request()->translations);
    }
}
