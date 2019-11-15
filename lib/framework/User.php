<?php
namespace framework;

use entity\Member;

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
    
    public function connect(Member $member) {
        $this->setAuthenticated();
        $this->setAttribute(array(
            'username' => $member->username(),
            'role' => $member->role(),
            'userId' => $member->id()
            ));
    }

    public function isAuthenticated() : bool
    {
        if ($this->hasAttribute('auth')) {
            return $this->app->httpRequest()->cookieData($this->ticketName) === $this->getAttribute('auth');
        }
        return false;
    }
    
    public function setAuthenticated() :void
    {
        $ticket = session_id().microtime().rand(0,9999999999);
        $ticket = hash('sha512', $ticket);

        $this->app->httpResponse()->setCookie($this->ticketName, $ticket, time() + 60*15, '/'); // Expire au bout de 15 min
        $this->setAttribute(array('auth' => $ticket));

    }

    public function setAttribute(array $values) :void
    {
        foreach ($values as $attr => $value) {
            if (!is_string($attr) || is_numeric($attr) || empty($attr))
            {
                throw new \InvalidArgumentException('Le nom de l\'attribut doit être une chaine de caractères non nulle');
            }

            $_SESSION[$attr] = $value;
        }
    }
    
    public function rememberMeToken(HTTPRequest $request, $memberRole) {
        if ($request->postData('remember') !== null) {
            $connexionId = uniqid('', true);
            $this->app->httpResponse()->setCookie($this->idCookieName, $connexionId, time() + 31*24*3600, '/');
            $this->app->httpResponse()->setCookie($this->roleCookieName, $memberRole, time() + 31*24*3600, '/');
            return $connexionId;
        }
        return null;
    }
    
    public function tryToReconnect(string $entity, Controller $controller) {

        $member = null;

        if ($this->app->httpRequest()->cookieExists($this->idCookieName) AND $this->app->httpRequest()->cookieExists($this->roleCookieName)) {

            $manager = $controller->managers()->getManagerOf($entity);

            $member = $manager->checkConnexionId($this->app->httpRequest()->cookieData($this->idCookieName));
            
            if ($member !== null) {
                if ($member->deleteDate() === null) {
                    $this->connect($member);
                }
                $this->app->httpResponse()->redirect('/');
            }
        } else {
            $this->setAttribute(array('auth' => 'visitor'));
        }
    }

    public function disconnect()
    {
        $_SESSION = [];
        session_destroy();


        $cookies = [$this->ticketName, $this->idCookieName, $this->roleCookieName];
        
        foreach ($cookies as $value) {
            $this->app->httpResponse()->setCookie($value, '');
        }
    }

    public function ticketName(): string
    {
        return $this->ticketName;
    }
    
}
