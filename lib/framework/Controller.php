<?php
namespace framework;

use model\PostManager;
use model\RecruiterManager;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class Controller extends ApplicationComponent
{
    protected $action = '';
    protected $module = '';
    protected $page = null;
    protected $view = '';
    protected $postManager = null;
    protected $recruiterManager = null;
    protected $loader;
    protected $twig;

    public function __construct(Application $app, $module, $action, array $dbConnexion)
    {
        parent::__construct($app);

        $this->postManager = new PostManager(PDOFactory::getMysqlConnexion($dbConnexion));
        $this->recruiterManager = new RecruiterManager(PDOFactory::getMysqlConnexion($dbConnexion));
        $this->page = new Page($app);
        $this->module = $module;
        $this->action = $action;
        $this->view = $action;
        $this->loader = new FilesystemLoader('../templates');
        $this->twig = new Environment($this->loader, [
            'debug' => true,
            'cache' => false //'/tmp'
        ]);
        $this->twig->addExtension(new Extension());
    }

    public function page()
    {
        return $this->page;
    }

    public function errorPage($intro, $message)
    {       
        $this->page->addVars('intro', $intro);
        $this->page->addVars('message', $message);

        $this->page->setTabTitle('Erreur');

        $this->page->setContent(__DIR__.'/../../Errors/modelError.php');
        $this->page->generate();
    }

    public function execute()
    {
        $method = 'execute'.ucfirst($this->action);

        if (!is_callable([$this, $method]))
        {
            throw new \RuntimeException('L\'action "'.$this->action.'" n\'est pas définie sur ce module');
        }

        if (empty($_SESSION)) {
            $this->defineUser();
        }

        $this->$method($this->app->httpRequest());
    }
    
    public function defineUser() {  
        $auth = $this->app->httpRequest()->cookieData('auth');

        if ($auth !== null) {
            $member = $this->memberManager->checkConnexionId($auth);
            if ($member !== null) {
                if ($member->deleteDate() === null) {
                    $this->app->user()->setAuthenticated(true);
                    $this->app->user()->setAttribute('id', $member->id());
                    $this->app->user()->setAttribute('pseudo', $member->pseudo());
                    $this->app->user()->setAttribute('privilege', $member->privilege());
                }
            }
        }  
    }
}