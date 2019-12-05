<?php

namespace yannsjobs\modules\candidates;

use framework\Controller;
use framework\HTTPRequest;


class CandidatesController extends Controller
{
    use \framework\MemberEditor;
    
    public function executeProfile(HTTPRequest $request) {
        $nbSavedPosts = null;
        $candidate = $this->managers->getManagerOf('Member')->getSingle($this->app->user()->getAttribute('userId'));
                
        if ($candidate !== null) {
            $filters['candidateId'] = '=' . $candidate->id();
            $nbSavedPosts = $this->managers->getManagerOf('SavedPost')->count($filters);
        } else {
            $errors[] = 'Impossible de trouver le profil de candidat demandé,';
            $errors[] = 'Contactez l\'administrateur.';
        }

        $this->page->setTemplate('profile/candidate.twig');

        $this->page->addVars(array(
            'user' => $this->app->user(),
            'candidate' => $candidate,
            'nbSavedPosts' => $nbSavedPosts,
            'title' => $candidate->userName() . ' | YannsJobs',
            'errors' => isset($errors) ? $errors : null
        )); 
    }
    
    public function executeUpdateSavedPosts(HTTPRequest $request) {
        $action = $request->getData('action');
        $post = $this->managers->getManagerOf('Post')->getSingle($request->getData('post'));
        
        if ($post === null) {
            return $this->app->httpResponse()->redirect404();
        }
        
        $this->app->httpResponse()->addHeader('Content-Type: application/json');
        
        if ($this->app->user()->getAttribute('role') === 'candidate') {
            $this->managers->getManagerOf('SavedPost')->$action($this->app->user()->getAttribute('userId'), $post->id());
            $json = json_encode(array('success' => $action));
        } else {
            $json = json_encode(array('redirect' => '/candidate'));
        }      
        
        $this->page->setContent($json);
    }
    
    public function executeEditCandidate(HTTPRequest $request) {
        $inputs = $this->app->config()->getFormConfigJSON('inputs', ['lastname', 'firstname', 'phone', 'email', 'passControl']);
        
        $this->executeEditProfile($request, $inputs);
    } 
    
}
