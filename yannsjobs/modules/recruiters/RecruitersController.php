<?php

namespace yannsjobs\modules\recruiters;

use framework\Controller;
use framework\HTTPRequest;

class RecruitersController extends Controller
{
    use \framework\MemberEditor;
    
    public function executeProfile(HTTPRequest $httpRequest) {
        $posts = [];
        $recruiter = $this->managers->getManagerOf('Member')->getSingle($this->app->user()->getAttribute('userId'));
        
        $nbLastPosts = $this->app->config()->get('display', 'last_posts');
        
        if ($recruiter !== null) {
            $filters['recruiterId'] = '=' .$recruiter->id();
            $nbPosts['total'] = $this->managers->getManagerOf('Post')->count($filters);
            $nbCandidacies = $this->managers->getManagerOf('Candidacy')->count($filters);
            $filters['expirationDate'] = '>NOW()';
            $posts = $this->managers->getManagerOf('Post')->getList($filters, 0, $nbLastPosts);
            $nbPosts['active'] = $this->managers->getManagerOf('Post')->count($filters);
        } else {
            $errors[] = 'Impossible de trouver le profil de recruteur demandé,';
            $errors[] = 'Contactez l\'administrateur.';
        }

        $this->page->setTemplate('profile/recruiter.twig');

        $this->page->addVars(array(
            'user' => $this->app->user(),
            'recruiter' => $recruiter,
            'posts' => $posts,
            'nbPosts' => isset($nbPosts) ? $nbPosts : null,
            'nbCandidacies' => isset($nbCandidacies) ? $nbCandidacies : 0,
            'title' => $recruiter->userName() . ' | YannsJobs',
            'errors' => isset($errors) ? $errors : null
        )); 
    }
    
    public function executeEditRecruiter(HTTPRequest $request) {
        $inputs = $this->app->config()->getFormConfigJSON('inputs', ['username', 'email', 'passControl']);
        
        $this->executeEditProfile($request, $inputs);
    } 
    
}
