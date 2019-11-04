<?php
// Important de préciser le namespace pour le fonctionnement de l'autoload
namespace framework;

class HTTPRequest extends ApplicationComponent
{
    // Obtenir le cookie
    public function cookieData($key)
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
    }

    //vérifier l'existence du cookie
    public function cookieExists($key)
    {
        return isset($_COOKIE[$key]);
    }

    // Obtenir les données GET
    public function getData($key)
    {
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }

    //vérifier l'existence des données GET
    public function getExists($key)
    {
        return isset($_GET[$key]);
    }

    // Obtenir la méthode de la requête
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    // Obtenir les données POST
    public function postData($key)
    {
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }

    //vérifier l'existence des données POST
    public function postExists($key)
    {
        return isset($_POST[$key]);
    }

    // Obtenir l'url de la requête
    public function requestURI()
    {
        return $_SERVER['REQUEST_URI'];
    }
}