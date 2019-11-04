<?php
namespace framework;

class Router
{
    protected $routes = [];

    const NO_ROUTE = 1;
    
    public function addRoute(Route $route)
    {
        // On controle que la route ne soit pas déjà dans la liste
        if(!in_array($route, $this->routes))
        {
            $this->routes[] = $route; // On l'ajoute.
        }
    }

    public function getRoute($url)
    {
        foreach ($this->routes as $route)
        {
            // Si la route correspond à l'URL
            if (($varsValues = $route->match($url)) !== false)
            {
                // Si elle a des variables
                if ($route->hasVars())
                {
                    $varsNames = $route->varsNames();
                    $listVars = [];

                    // On crée un nouveau tableau clé/valeur
                    // (clé = nom de la variable, valeur = sa valeur)
                    foreach ($varsValues as $key => $match)
                    {
                        // La première valeur contient entièrement la chaine capturée (voir la doc sur preg_match)
                        if ($key !== 0)
                        {
                            $listVars[$varsNames[$key - 1]] = $match;
                        }
                    }

                // On assigne ce tableau de variables a la route
                $route->setVars($listVars);
                }

            return $route;
            }
        }

        throw new \RuntimeException('Aucune route ne correspond à l\'URL', self::NO_ROUTE); 
    }
}