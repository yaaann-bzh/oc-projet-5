<?php
namespace framework;

session_start();

class User extends ApplicationComponent
{
    private $ticketName = 'a_ya';

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

        $this->app->httpResponse()->setCookie($this->ticketName, $ticket, time() + (60 * 15)); // Expire au bout de 15 min
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

    public function disconnect()
    {
        var_dump('disconnect');
        $_SESSION = [];
        session_destroy();

        $this->app->httpResponse()->setCookie($this->ticketName, '');
    }

    public function ticketName(): string
    {
        return $this->ticketName;
    }
    
}
