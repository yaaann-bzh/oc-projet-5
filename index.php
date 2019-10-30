<?php
require 'vendor/autoload.php';

use \Michelf\Markdown;

echo Markdown::defaultTransform("Salut les gens j'essaie le **markdown**");

