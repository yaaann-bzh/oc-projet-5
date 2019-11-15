<?php
namespace yannsjobs\modules\connexion;

use framework\HTTPRequest;
use framework\Controller;
use framework\Form;

class ConnexionController extends Controller
{
    public function executeIndex(HTTPRequest $request)
    {
        $inputs = $this->app->config()->getFormConfig('inputs', ['email', 'pass']);
        
        $form = new Form($inputs);

        if ($request->postExists('submit') AND $form->isValid($request)) {

            $memberManager = $this->managers->getManagerOf('Member');
            $member = $form->checkPassword($memberManager);

            if ($member !== null) {
                $this->app->user()->connect($member);

                $rememberMeTokenId = $this->app->user()->rememberMeToken($request, $member->role());
                $memberManager->saveConnexionId($member->id(), $rememberMeTokenId);

                $location = '/' . $member->role(). '/home';
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
}
