<?php


namespace yannsjobs\modules\candidacies;

use framework\Controller;
use framework\HTTPRequest;
use entity\Candidacy;
use framework\Form;


class CandidaciesController extends Controller 
{
    public function executeApply(HTTPRequest $request) {
        $post = $this->managers->getManagerOf('Post')->getSingle((int)$request->getData('post'));
        $member = $this->managers->getManagerOf('Member')->getSingle((int)$this->app->user()->getAttribute('userId'));
        $this->app->checkContentAccess($post, $member);
        
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

                return $this->app->httpResponse()->redirect('/candidate/candidacies-1');
                
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
}
