<?php
namespace framework;

session_start();

class User extends ApplicationComponent
{
    private $ticketName;
    private $idCookieName;
    private $roleCookieName;
    
    public function __construct(Application $app) {
        parent::__construct($app);
        
        $this->ticketName = $this->app->config()->get('cookies_names', 'ticket_name');
        $this->idCookieName = $this->app->config()->get('cookies_names', 'remember_me');
        $this->roleCookieName = $this->app->config()->get('cookies_names', 'user_role');
    }

    public function hasAttribute($attr)
    {
        return isset($_SESSION[$attr]);
    }

    public function getAttribute($attr)
    {
        return isset($_SESSION[$attr]) ? $_SESSION[$attr] : null;
    }

    public function isAuthenticated() : bool
    {
        if ($this->hasAttribute('auth')) {
            return $this->app->httpRequest()->cookieData($this->ticketName) === $this->getAttribute('auth');
        }
        return false;
    }

    public function isAdmin() : bool
    {

    }
    
    public function setAuthenticated() :void
    {
        $ticket = session_id().microtime().rand(0,9999999999);
        $ticket = hash('sha512', $ticket);

        $this->app->httpResponse()->setCookie($this->ticketName, $ticket, time() + 60*15); // Expire au bout de 15 min
        $this->setAttribute(array('auth' => $ticket));

        //var_dump($this->app->httpRequest()->cookieData($this->ticketName));
        //var_dump($this->getAttribute('auth'));
    }

    public function setAttribute(array $values) :void
    {
        foreach ($values as $attr => $value) {
            if (!is_string($attr) || is_numeric($attr) || empty($attr))
            {
                throw new \InvalidArgumentException('Le nom de l\'attribut doit être une chaine de caractères non nulle');
            }
            //var_dump('setAttribute : ' . $attr);
            $_SESSION[$attr] = $value;
        }
    }
    
    public function tryToReconnect(Controller $controller) {
        var_dump('trytoreconnect');

        $member = null;

        if ($this->app->httpRequest()->cookieExists($this->idCookieName) AND $this->app->httpRequest()->cookieExists($this->roleCookieName)) {
            $memberManager = $controller->managers()->getManagerOf($this->app->httpRequest()->cookieData($this->roleCookieName));
            if ($memberManager !== null){
                var_dump($memberManager);
                $member = $memberManager->checkConnexionId($this->app->httpRequest()->cookieData($this->idCookieName));
            }
            
            if ($member !== null) {
                var_dump($member);
                if ($member->deleteDate() === null) {
                    $this->setAuthenticated();
                    $this->setAttribute(array(
                            'username' => $member->username(),
                            'role' => $this->app->name()
                            ));
                }
                $this->app->httpResponse()->redirect('/');
            }
        } else {
            $this->setAttribute(array('auth' => 'visitor'));
        }
    }

    public function disconnect()
    {
        var_dump('disconnect');
        $_SESSION = [];
        session_destroy();

        $cookies = array (
            $this->ticketName => '',
            $this->idCookieName => '',
            $this->roleCookieName => ''
        );
        
        foreach ($cookies as $key => $value) {
            $this->app->httpResponse()->setCookie($key, $value);
        }
    }

    public function ticketName(): string
    {
        return $this->ticketName;
    }
    
}
