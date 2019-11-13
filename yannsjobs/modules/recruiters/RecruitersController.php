<?php

namespace yannsjobs\modules\recruiters;

use framework\Controller;
use framework\HTTPRequest;

class RecruitersController extends Controller
{
    public function executeHome(HTTPRequest $httpRequest) {
        $errors = [];
        $posts = [];
        $nbPosts = [];
        $recruiter = $this->managers->getManagerOf('Recruiter')->getSingle($this->app->user()->getAttribute('userId'));
        $nbLastPosts = $this->app->config()->get('display', 'last_posts');
        
        if ($recruiter !== null) {
            $filters['recruiterId'] = '=' .$recruiter->id();
            $nbPosts['total'] = $this->managers->getManagerOf('Post')->count($filters);
            $filters['expirationDate'] = '>NOW()';
            $posts = $this->managers->getManagerOf('Post')->getList(0, $nbLastPosts, $filters);
            $nbPosts['active'] = $this->managers->getManagerOf('Post')->count($filters);
        } else {
            $errors[] = 'Impossible de trouver le profil de recruteur demandé,';
            $errors[] = 'Contactez l\'administrateur.';
        }

        $this->page->setTemplate('recruiter.twig');

        $this->page->addVars(array(
            'user' => $this->app->user(),
            'recruiter' => $recruiter,
            'posts' => $posts,
            'nbPosts' => $nbPosts,
            'title' => $recruiter->userName() . ' | YannsJobs',
            'errors' => $errors
        )); 
        //var_dump($this->page->vars());
    }
    
}
