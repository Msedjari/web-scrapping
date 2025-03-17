<?php

namespace App\Controller;

class LanguageController
{
    protected $supportedLanguages = ['en', 'es'];
    protected $translations;

    public function __construct()
    {
        $locale = $this->detectUserLocale();
        $this->translations = $this->loadTranslations($locale);
    }

    public function setLanguage($locale)
    {
        if (in_array($locale, $this->supportedLanguages)) {
            $_SESSION['locale'] = $locale;
        }
    }

    public function detectUserLocale()
    {

        // Check if user-selected language is in session
        if (isset($_SESSION['locale']) && in_array($_SESSION['locale'], $this->supportedLanguages)) {
            return $_SESSION['locale'] ?? 'en';
        }

    }

    public function loadTranslations($locale)
    {
        $translationsFile = __DIR__ . "/../../translations/{$locale}.php";
        return file_exists($translationsFile) ? include $translationsFile : [];
    }

    public function getTranslations()
    {
        return $this->translations;
    }


}