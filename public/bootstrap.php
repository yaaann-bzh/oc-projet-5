<?php
const DEFAULT_app = 'Frontend';
 
// Si l'application n'est pas valide, on va charger l'application par défaut qui se chargera de générer une erreur 404
if (!isset($_GET['app']) || !file_exists(__DIR__ . '/../yannsjobs/')) $_GET['app'] = DEFAULT_app;

require __DIR__.'/../vendor/autoload.php';

$appClass = 'yannsjobs\\'.$_GET['app'];

$app = new $appClass;
$app->run();

