<?php

namespace App\Controller; // Definimos el namespace para la clase LanguageController

class LanguageController // Definimos la clase LanguageController
{
    protected $supportedLanguages = ['en', 'es']; // Declaramos un array de idiomas soportados
    protected $translations; // Declaramos una propiedad para almacenar las traducciones

    public function __construct() // Definimos el constructor de la clase
    {
        $locale = $this->detectUserLocale(); // Detectamos el idioma del usuario
        $this->translations = $this->loadTranslations($locale); // Cargamos las traducciones para el idioma detectado
    }

    public function setLanguage($locale) // Método para establecer el idioma
    {
        if (in_array($locale, $this->supportedLanguages)) { // Verificamos si el idioma está soportado
            $_SESSION['locale'] = $locale; // Establecemos el idioma en la sesión
        }
    }

    public function detectUserLocale() // Método para detectar el idioma del usuario
    {
        // Check if user-selected language is in session
        if (isset($_SESSION['locale']) && in_array($_SESSION['locale'], $this->supportedLanguages)) { // Verificamos si el idioma está en la sesión y es soportado
            return $_SESSION['locale'] ?? 'en'; // Retornamos el idioma de la sesión o 'en' por defecto
        }
    }

    public function loadTranslations($locale) // Método para cargar las traducciones
    {
        $translationsFile = __DIR__ . "/../../translations/{$locale}.php"; // Definimos la ruta del archivo de traducciones
        return file_exists($translationsFile) ? include $translationsFile : []; // Incluimos el archivo de traducciones si existe, de lo contrario retornamos un array vacío
    }

    public function getTranslations() // Método para obtener las traducciones
    {
        return $this->translations; // Retornamos las traducciones cargadas
    }
}