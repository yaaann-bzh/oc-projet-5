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
        $post->setApplied($this->managers->getManagerOf('Candidacy')->exists($member->id(), $post->id()));
        $inputs = $this->app->config()->getFormConfigJSON('inputs', ['cover', 'resume']);
     
        $form = new Form($inputs);

        if ($request->postExists('submit') AND $form->isValid($request)) {
            $values = $form->values();
            $values['candidateId'] = (int)$member->id();
            $values['recruiterId'] = (int)$post->recruiterId();
            $values['postId'] = (int)$post->id();

            try {
                $path = __DIR__ . '/resume';
                $id = $post->id() . '_' . $member->id();
                $form->file('resume')->save($path, 'resume_', $id);
                $values['resumeFile'] = $form->file('resume')->name();
                $candidacy = new Candidacy($values);
                $this->managers->getManagerOf('Candidacy')->add($candidacy);

                return $this->app->httpResponse()->redirect('');
                
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        } else {
            $errors = $form->errors();
        }

        $this->page->setTemplate('candidacies/apply.twig');

        $this->page->addVars(array(
            'post' => $post,
            'user' => $this->app->user(),
            'member' => $member,
            'title' => 'Candidature | YannsJobs',
            'values' => $form->values(),
            'errors' => isset($errors) ? $errors : null
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
        
        $pager = new Pager($this->app(), $posts);
        $pager->setListPagination((int)$request->getData('index'), $this->app->config()->get('display', 'nb_posts'));

        $this->page->setTemplate('candidacies/list.twig');

        $this->page->addVars(array(
            'pagination' => $pager->pagination(),
            'user' => $this->app->user(),
            'postsList' => $pager->list(),
            'title' => 'Candidatures recues | YannsJobs',
            'errors' => array_merge($pager->errors(), $errors)
        ));
    }
}
