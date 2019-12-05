<?php


namespace yannsjobs\modules\candidacies;

use framework\Controller;
use framework\HTTPRequest;
use entity\Candidacy;
use framework\Form;
use framework\Pager;


class CandidaciesController extends Controller 
{
    public function executeApply(HTTPRequest $request) {
        $post = $this->managers->getManagerOf('Post')->getSingle((int)$request->getData('post'));
        $member = $this->managers->getManagerOf('Member')->getSingle((int)$this->app->user()->getAttribute('userId'));
        $this->app->checkContentAccess($post, $member);
        $post->setSaved($this->managers->getManagerOf('SavedPost')->exists($member->id(), $post->id()));
        $post->setApplied($this->managers->getManagerOf('Candidacy')->exists($member->id(), $post->id()));
        $inputs = $this->app->config()->getFormConfigJSON('inputs', ['cover', 'resume']);
     
        $form = new Form($inputs);

        if ($request->postExists('submit') AND $form->isValid($request)) {
            $form->setValues('candidateId', (int)$member->id());
            $form->setValues('recruiterId', (int)$post->recruiterId());
            $form->setValues('postId', (int)$post->id());

            try {
                $path = __DIR__ . '/resume';
                $id = $member->lastname() . $post->id() . $member->id();
                $form->setValues('resumeFile', $form->file('resume')->save($path, 'resume_', $id));
                $candidacy = new Candidacy($form->values());
                $this->managers->getManagerOf('Candidacy')->add($candidacy);

                return $this->app->httpResponse()->redirect('');
                
            } catch (\Exception $e) {
                $form->setErrors('',  $e->getMessage());
            }
        }

        $this->page->setTemplate('candidacies/apply.twig');

        $this->page->addVars(array(
            'post' => $post,
            'user' => $this->app->user(),
            'member' => $member,
            'title' => 'Candidature | YannsJobs',
            'values' => $form->values(),
            'errors' => $form->errors()
        ));
    }
    
    public function executeList(HTTPRequest $request) {
        $posts = [];
        $errors = [];
        $recruiter = $this->managers->getManagerOf('Member')->getSingle($this->app->user()->getAttribute('userId'));

        if ($recruiter !== null) {
            $posts = $this->managers->getManagerOf('Candidacy')->getPosts(array('recruiterId' => '=' . $recruiter->id()));
            
            foreach ($posts as $key => $post) {
                $posts[$key]['post'] = $this->managers->getManagerOf('Post')->getSingle($key);
            }
        } else {
            $errors[] = 'Impossible de trouver le profil de candidat demandé,';
            $errors[] = 'Contactez l\'administrateur.';
        }
        
        $pager = new Pager($this->app(), $posts, (int)$request->getData('index'), $this->app->config()->get('display', 'nb_posts'));

        $this->page->setTemplate('candidacies/list.twig');

        $this->page->addVars(array(
            'pagination' => $pager->pagination(),
            'user' => $this->app->user(),
            'postsList' => $pager->list(),
            'title' => 'Candidatures recues | YannsJobs',
            'errors' => array_merge($pager->errors(), $errors)
        ));
    }
    
    public function executeListByPost(HTTPRequest $request) {
        $post = $this->managers->getManagerOf('Post')->getSingle((int)$request->getData('post'));
        $recruiter = $this->managers->getManagerOf('Member')->getSingle($this->app->user()->getAttribute('userId'));
        $this->app->checkContentAccess($post, $recruiter);

        $filters = array(
            'recruiterId' => '=' . $recruiter->id(),
            'postId' => '=' . $post->id()
                );
        $candidacies = $this->managers->getManagerOf('Candidacy')->getList($filters);
        
        foreach ($candidacies as $candidacy) {
            $candidacy->setCandidate($this->managers->getManagerOf('Member')->getSingle($candidacy->candidateId()));
        }
        
        $pager = new Pager($this->app(), $candidacies, (int)$request->getData('index'), $this->app->config()->get('display', 'nb_candidacies'));

        $this->page->setTemplate('candidacies/list.twig');

        $this->page->addVars(array(
            'pagination' => $pager->pagination(),
            'user' => $this->app->user(),
            'candidacies' => $pager->list(),
            'post' => $post,
            'title' => 'Candidatures recues | YannsJobs',
            'errors' => $pager->errors()
        ));
    }
    
    public function executeDownload(HTTPRequest $request) {
        $candidacy = $this->managers->getManagerOf('Candidacy')->getSingle((int)$request->getData('candidacy'));
        $post = $this->managers->getManagerOf('Post')->getSingle($candidacy->postId());
        $recruiter = $this->managers->getManagerOf('Member')->getSingle($this->app->user()->getAttribute('userId'));
        $this->app->checkContentAccess($post, $recruiter);
        
        $lastSlashPos = strrpos($candidacy->resumeFile(), '/');
        
        $file = substr($candidacy->resumeFile(), $lastSlashPos+1);
        
        if (file_exists($candidacy->resumeFile())) {
            $fileDownload = \Apfelbox\FileDownload\FileDownload::createFromFilePath($candidacy->resumeFile());
            $fileDownload->sendDownload($file);
        } else {
            $this->app->httpResponse()->redirect404();
        }        
    }
    
    public function executeChangeStatus(HTTPRequest $request) {
        $candidacy = $this->managers->getManagerOf('Candidacy')->getSingle((int)$request->getData('candidacy'));
        $post = $this->managers->getManagerOf('Post')->getSingle($candidacy->postId());
        $recruiter = $this->managers->getManagerOf('Member')->getSingle($this->app->user()->getAttribute('userId'));

        $this->app->checkContentAccess($post, $recruiter);
        
        $this->managers->getManagerOf('Candidacy')->changeReadStatus($candidacy->id());
        
        $this->app->httpResponse()->send204();
    }
}
