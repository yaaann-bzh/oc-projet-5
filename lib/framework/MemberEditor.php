<?php


namespace framework;


trait MemberEditor {

    public function executeEditProfile(HTTPRequest $request, array $inputs) {
        $memberManager = $this->managers->getManagerOf('Member');
        $member = $memberManager->getSingle($this->app->user()->getAttribute('userId'));             
        
        $form = new Form($inputs);
        $form->getValues($member);
        
        if ($request->postExists('submit') AND $form->isValid($request, $memberManager, $member->id())) {
            
            try {
                $form->unsetValues('passControl');
                $memberManager->update($member->id(), $form->values());

                return $this->app->httpResponse()->redirect('/' . $member->role() . '/profile');
                
            } catch (\Exception $e) {
                $form->setErrors('',  $e->getMessage());
            }
        }
        
        $this->page->setTemplate('profile/edit.twig');

        $this->page->addVars(array(
            'user' => $this->app->user(),
            'title' => 'Mettre à jour | YannsJobs',
            'values' => isset($form) ? $form->values() : null,
            'errors' => $form->errors()
        ));
    }
    
    public function executeEditPassword(HTTPRequest $request) {
        $memberManager = $this->managers->getManagerOf('Member');
        $member = $memberManager->getSingle($this->app->user()->getAttribute('userId'));             
        $inputs = $this->app->config()->getFormConfigJSON('inputs', ['pass', 'confirm', 'passControl']);
        
        $form = new Form($inputs);
        
        if ($request->postExists('submit') AND $form->isValid($request, $memberManager)) {
            
            try {
                $form->unsetValues('passControl');
                $memberManager->update($member->id(), $form->values());

                return $this->app->httpResponse()->redirect('/' . $member->role() . '/profile');
                
            } catch (\Exception $e) {
                $form->setErrors('',  $e->getMessage());
            }
        }
        
        $this->page->setTemplate('profile/edit_password.twig');

        $this->page->addVars(array(
            'user' => $this->app->user(),
            'title' => 'Mot de passe | YannsJobs',
            'errors' => $form->errors()
        ));
    }  
}
