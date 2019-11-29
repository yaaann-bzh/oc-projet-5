<?php

namespace yannsjobs\modules\candidates;

use framework\Controller;
use framework\HTTPRequest;

class CandidatesController extends Controller
{
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
        $page = $request->getData('page');
        $index = $request->getData('index');
        $ext = $request->getData('ext');

        if ($post !== null) {
            $this->managers->getManagerOf('SavedPost')->$action($this->app->user()->getAttribute('userId'), $post->id());
            if ($page === null){
                return $this->app->httpResponse()->redirect('/');
            }
            
            $location = '/' . $page . '-';
            
            $num = (is_null($index) OR empty($index)) ? '1' : $index;

            $location .= $num;
            
            if ($ext !== null) {
                $location .= '/' . $ext . '-' . $post->id();

            }

            return $this->app->httpResponse()->redirect($location);
        }
        
        $this->app->httpResponse()->redirect404();
    }    
}
