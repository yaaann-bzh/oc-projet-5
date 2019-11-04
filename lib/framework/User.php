<?php
namespace framework;

session_start();

class User
{
    public function getAttribute($attr)
    {
        return isset($_SESSION[$attr]) ? $_SESSION[$attr] : null;
    }

    public function isAuthenticated()
    {
        return isset($_SESSION['auth']) && $_SESSION['auth'] === true;
    }

    public function isAdmin()
    {
        return isset($_SESSION['privilege']) && $_SESSION['privilege'] !== null;
    }
    
    public function setAuthenticated($authenticated = true)
    {
        if (!is_bool($authenticated))
        {
            throw new \InvalidArgumentException('La valeur spécifiée à la méthode Member::setAuthenticated() doit être un boolean');
        }

        $_SESSION['auth'] = $authenticated;
    }

    public function setAttribute($attr, $value)
    {
        $_SESSION[$attr] = $value;
    }
    
}
