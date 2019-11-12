<?php
namespace framework;


class Controller extends ApplicationComponent
{
    protected $action = '';
    protected $module = '';
    protected $page = null;
    protected $view = '';
    protected $managers = null;

    public function __construct(Application $app, $module, $action, array $dbConnexion)
    {
        parent::__construct($app);

        $this->managers = new Managers(PDOFactory::getMysqlConnexion($dbConnexion));
        $this->page = new Page($app);
        $this->module = $module;
        $this->action = $action;
        $this->view = $action;
    }

    public function page()
    {
        return $this->page;
    }
    
    public function managers() {
        return $this->managers;
    }

    public function errorPage($intro, $message)
    {       

    }

    public function execute()
    {
        $method = 'execute'.ucfirst($this->action);

        if (!is_callable([$this, $method]))
        {
            throw new \RuntimeException('L\'action "'.$this->action.'" n\'est pas définie sur ce module');
        }

        $this->$method($this->app->httpRequest());
    }
}