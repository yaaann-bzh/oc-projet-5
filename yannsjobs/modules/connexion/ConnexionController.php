<?php
namespace yannsjobs\modules\connexion;

use framework\HTTPRequest;
use framework\Controller;

class ConnexionController extends Controller
{
    public function executeIndex(HTTPRequest $request)
    {
        $memberManager = $this->managers->getManagerOf('Member');
        $connectFailures = [];

        if ($request->postExists('login') AND $request->postExists('password')) {
            $login = $request->postData('login');
            $password = $request->postData('password');
            $member = $memberManager->getSingle($memberManager->getId($login));

            if ($member !== null) {
                if (password_verify($password, $member->pass())) {
                    $this->app->user()->setAuthenticated();
                    $this->app->user()->setAttribute(array(
                        'username' => $member->username(),
                        'role' => $member->role(),
                        'userId' => $member->id()
                        ));

                    if ($request->postData('remember') !== null) {
                        $connexionId = uniqid('', true);
                        $this->app->httpResponse()->setCookie($this->app->config()->get('cookies_names', 'remember_me'), $connexionId, time() + 31*24*3600, '/');
                        $this->app->httpResponse()->setCookie($this->app->config()->get('cookies_names', 'user_role'), $member->role(), time() + 31*24*3600, '/');
                        $memberManager->saveConnexionId($member->id(), $connexionId);
                    }

                    $location = '/' . strtolower($this->app->name()). '/home';
                    return $this->app->httpResponse()->redirect($location);

                } else {
                    $connectFailures[] = 'Identifiant ou mot de passe incorrect';
                }
            } else {
                $connectFailures[] = 'Identifiant ou mot de passe incorrect';
            }
        }

        $this->page->setTemplate('connexion.twig');

        $this->page->addVars(array(
            'user' => $this->app->user(),
            'title' => 'Connection | YannsJobs',
            'connectFailures' => $connectFailures
        ));
    }

    public function executeDisconnect(HTTPRequest $request)
    {
        $this->app->user()->disconnect();
        
        $this->app->httpResponse()->redirect('/');

    }
}
