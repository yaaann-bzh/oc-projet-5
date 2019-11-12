<?php
namespace framework;


abstract class Application
{
    protected $httpRequest;
    protected $httpResponse;
    protected $name;
    protected $user;
    protected $config;

    public function __construct()
    {
        $this->httpRequest = new HTTPRequest($this);
        $this->httpResponse = new HTTPResponse($this);
        $this->config = new Config($this);
        $this->user = new User($this);
    }
    
    public function getController()
    {
        $router = new Router;
    
        $xml = new \DOMDocument;
        $xml->load(__DIR__.'/../../config/routes.xml');

        $routes = $xml->getElementsByTagName('route');
        foreach ($routes as $route ) {
            $vars = [];
            // On regarde si des variables sont présentes dans l'URL.
            if ($route->hasAttribute('vars'))
            {
                $vars = explode(',', $route->getAttribute('vars'));
            }
            // On ajoute la route au routeur.
            $router->addRoute(new Route($route->getAttribute('url'), $route->getAttribute('module'), $route->getAttribute('action'), $vars));
        }

        try
        {
            // On récupère la route correspondante à l'URL.
            $matchedRoute = $router->getRoute($this->httpRequest->requestURI());
        }
        catch (\RuntimeException $e)
        {
            if ($e->getCode() == Router::NO_ROUTE)
            {
                // Si aucune route ne correspond, c'est que la page demandée n'existe pas.
                $this->httpResponse->redirect404();
            }
        }

        // On ajoute les variables de l'URL au tableau $_GET.
        $_GET = array_merge($_GET, $matchedRoute->vars());

        $module = $matchedRoute->module();
        $action = $matchedRoute->action();

        // On instancie le contrôleur.
        $controllerClass = 'yannsjobs\\modules\\'.$module.'\\'.ucfirst($module).'Controller';
        return new $controllerClass($this, $module, $action, $this->getDBConnexion());
    }

    public function name()
    {
        return $this->name;
    }
    
    public function user()
    {
        return $this->user;
    }

    public function config()
    {
        return $this->config;
    }

    public function getDBConnexion()
    {
        return array(
            'db_host' => $this->config->get('pdo', 'db_host'),
            'db_name' => $this->config->get('pdo', 'db_name'),
            'db_user' => $this->config->get('pdo', 'db_user'),
            'db_pass' => $this->config->get('pdo', 'db_pass')
        );
    }

    public function httpRequest()
    {
        return $this->httpRequest;
    }

    public function httpResponse()
    {
        return $this->httpResponse;
    }

    public function userConnect(Controller $controller)
    {
        if (empty($_SESSION)) {
            $this->user->tryToReconnect($controller);
        }

        //var_dump('is auth : ' . $this->user->isAuthenticated());

        if ($this->user->isAuthenticated()) {
            $this->user->setAuthenticated();
        } elseif ($this->httpRequest->cookieExists($this->user->ticketName())) {
            $this->user->disconnect();
        }
    }
}