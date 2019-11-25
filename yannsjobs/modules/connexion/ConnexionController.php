<?php
namespace yannsjobs\modules\connexion;

use framework\HTTPRequest;
use framework\Controller;
use framework\Form;
use entity\Member;

class ConnexionController extends Controller
{
    public function executeIndex(HTTPRequest $request)
    {
        $inputs = $this->app->config()->getFormConfigJSON('inputs', ['emailLogin', 'passLogin']);
        $form = new Form($inputs);

        if ($request->postExists('submit') AND $form->isValid($request)) {

            $memberManager = $this->managers->getManagerOf('Member');
            $member = $form->checkPassword($memberManager);

            if ($member !== null) {
                $this->app->user()->connect($member);

                $rememberMeTokenId = $this->app->user()->rememberMeToken($request, $member->role());
                $memberManager->saveConnexionId($member->id(), $rememberMeTokenId);

                $location = '/' . $member->role(). '/profile';
                return $this->app->httpResponse()->redirect($location);
            } 
        }
        
        $connectErrors = $form->errors();

        $this->page->setTemplate('connexion.twig');

        $this->page->addVars(array(
            'user' => $this->app->user(),
            'title' => 'Connection | YannsJobs',
            'connectErrors' => $connectErrors
        ));
    }

    public function executeDisconnect(HTTPRequest $request)
    {
        $this->app->user()->disconnect();
        
        $this->app->httpResponse()->redirect('/');

    }
    
    public function executeInscription(HTTPRequest $request) {
        
        if ($request->getExists('role')){
            $role = $request->getData('role');
            $page = 'inscription/inscription.twig';
        } else {
            $page = 'inscription/role_choice.twig';
        }
        
        $inputs = $this->app->config()->getFormConfigJSON('inputs', ['username', 'lastname', 'firstname', 'phone', 'email', 'pass', 'confirm', 'agree']);
        $form = new Form($inputs);
        
        if ($request->postExists('submit') AND $form->isValid($request, $this->managers->getManagerOf('Member'))) {
            
            try {
                $form->setValues('role', $role);
                $member = new Member($form->values());
                
                $this->managers->getManagerOf('Member')->add($member);

                return $this->app->httpResponse()->redirect('/' . $role);
                
            } catch (\Exception $e) {
                $form->setErrors('',  $e->getMessage());
            }
        }
        
        $this->page->setTemplate($page);

        $this->page->addVars(array(
            'user' => $this->app->user(),
            'role' => isset($role) ? $role : null,
            'title' => 'Inscription | YannsJobs',
            'values' => isset($form) ? $form->values() : null,
            'errors' => $form->errors()
        ));
    }
}
