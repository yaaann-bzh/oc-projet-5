<?php
namespace yannsjobs\modules\posts;

use framework\Controller;
use entity\Post;
use framework\Pager;
use framework\HTTPRequest;
use framework\Form;
use framework\Search;


class PostsController extends Controller
{
    public function executeIndex(HTTPRequest $request)
    {    
        $search = new Search (array(            
            'title' => $request->postData('titleSearch'),
            'location' => $request->postData('locationSearch')
        ));

        $search->setFilter('expirationDate', '>NOW()');
        
        $postsId = $this->managers->getManagerOf('Post')->getIdList('addDate DESC', $search->filters(), $search->search(), $search->charsReplace());

        $pager = new Pager($this->app(), $postsId, (int)$request->getData('index'), $this->app->config()->get('display', 'nb_posts'));

        $posts = $pager->getEntities($this->managers->getManagerOf('Post'));
        foreach ($posts as $post) {
            $post->setRecruiterName($this->managers->getManagerOf('Member')->getSingle($post->recruiterId())->username());
        }      
        
        if ($this->app->user()->getAttribute('role') === 'candidate') {   
            $savedPosts = $this->managers->getManagerOf('SavedPost')->getPostIdList($this->app->user()->getAttribute('userId'));
            foreach ($posts as $post) {
                $post->setSaved(in_array($post->id(), $savedPosts));
                $post->setApplied($this->managers->getManagerOf('Candidacy')->exists($this->app->user()->getAttribute('userId'), $post->id()));
            }
        }

        $this->page->setTemplate('home.twig');

        $this->page->addVars(array(
            'pagination' => $pager->pagination(),
            'search' => $search->search(),
            'user' => $this->app->user(),
            'postsList' => $posts,
            'activePost' => $request->getData('post'),
            'title' => 'Accueil | YannsJobs',
            'errors' => $pager->errors()
        ));
    }
    
    public function executeSavedList(HTTPRequest $request)
    {
        $savedPostsIds = [];
        $errors = [];
        $candidate = $this->managers->getManagerOf('Member')->getSingle($this->app->user()->getAttribute('userId'));

        if ($candidate !== null) {
            $savedPostsIds = $this->managers->getManagerOf('SavedPost')->getPostIdList($candidate->id());
        } else {
            $errors[] = 'Impossible de trouver le profil de candidat demandé,';
            $errors[] = 'Contactez l\'administrateur.';
        }
        
        $pager = new Pager($this->app(), $savedPostsIds, (int)$request->getData('index'), $this->app->config()->get('display', 'nb_posts'));
       
        $posts = $pager->getEntities($this->managers->getManagerOf('Post'));

        foreach ($posts as $post) {
            $post->setRecruiterName($this->managers->getManagerOf('Member')->getSingle($post->recruiterId())->username());
            $post->setApplied($this->managers->getManagerOf('Candidacy')->exists($candidate->id(), $post->id()));
        }

        $this->page->setTemplate('posts/posts_list.twig');

        $this->page->addVars(array(
            'pagination' => $pager->pagination(),
            'user' => $this->app->user(),
            'postsList' => $posts,
            'title' => 'Offres sauvegardées | YannsJobs',
            'errors' => array_merge($pager->errors(), $errors)
        ));
    }
    
    public function executePublication(HTTPRequest $request,array $values = [], Post $editedPost = null)
    {
        $inputs = $this->app->config()->getFormConfigJSON('inputs', ['title', 'location', 'duration', 'content']);
        
        $form = new Form($inputs, $values);

        if ($request->postExists('submit') AND $form->isValid($request)) {
            $form->setValues('recruiterId', (int)$this->app->user()->getAttribute('userId'));
            
            try {
                $post = new Post($form->values());
                !is_null($editedPost) ? $post->setId((int)$editedPost->id()): null;
                $this->managers->getManagerOf('Post')->process($post);

                return $this->app->httpResponse()->redirect('/post-' . $post->id());
                
            } catch (\Exception $e) {
                $form->setErrors('',  $e->getMessage());
            }
        } 

        $pageTitle = is_null($editedPost)? 'Nouvelle offre' : $editedPost->title();
        $this->page->setTemplate('posts/redaction.twig');

        $this->page->addVars(array(
            'user' => $this->app->user(),
            'title' => $pageTitle . ' | YannsJobs',
            'values' => $form->values(),
            'errors' => $form->errors()
        ));
    }
    
    public function executeCopy(HTTPRequest $request) {
        $postId = (int)$request->getData('post');
        $post = $this->managers->getManagerOf('Post')->getSingle($postId);
        $member = $this->managers->getManagerOf('Member')->getSingle($this->app->user()->getAttribute('userId'));
        $this->app->checkContentAccess($post, $member);
        $values = array(
            'title' => $post->title(),
            'location' => $post->location(),
            'content' => $post->content()
        );
        
        $this->executePublication($request, $values);
    }
    
    public function executeEdit(HTTPRequest $request) {
        $postId = (int)$request->getData('post');
        $post = $this->managers->getManagerOf('Post')->getSingle($postId);
        $member = $this->managers->getManagerOf('Member')->getSingle($this->app->user()->getAttribute('userId'));
        $this->app->checkContentAccess($post, $member);
        $values = array(
            'edition' => true,
            'title' => $post->title(),
            'location' => $post->location(),
            'content' => $post->content()
        );
        
        $this->executePublication($request, $values, $post);
    }
    
    public function executeHide(HTTPRequest $request) {
        $postId = (int)$request->getData('post');
        $post = $this->managers->getManagerOf('Post')->getSingle($postId);
        $member = $this->managers->getManagerOf('Member')->getSingle($this->app->user()->getAttribute('userId'));
        
        $this->app->checkContentAccess($post, $member);
        
        $errors = [];
        
        try {
            $this->managers->getManagerOf('Post')->setExpired($post->id());
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }
        
        $this->executeShow($request, $errors);
    }
    
    public function executeDelete(HTTPRequest $request) {
        $postId = (int)$request->getData('post');
        $post = $this->managers->getManagerOf('Post')->getSingle($postId);
        $member = $this->managers->getManagerOf('Member')->getSingle($this->app->user()->getAttribute('userId'));
        
        $this->app->checkContentAccess($post, $member);
  
        $errors = [];

        try {
            $resumes = new \framework\Files($this->managers->getManagerOf('Candidacy')->getResumeFileList(array( 'postId=' => $post->id())));
            $resumes->delete();
            $this->managers->getManagerOf('Post')->delete($post->id());           
            return $this->app->httpResponse()->redirect('/recruiter/postslist/index-1');
            
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }
        
        $this->executeShow($request, $errors);
    }

    public function executeList(HTTPRequest $request) {
        
        $recruiter = $this->managers->getManagerOf('Member')->getSingle($this->app->user()->getAttribute('userId'));
        $filters['recruiterId'] = '=' . $recruiter->id();
        
        $posts = $this->managers->getManagerOf('Post')->getList($filters);

        $pager = new Pager($this->app(), $posts, (int)$request->getData('index'), $this->app->config()->get('display', 'nb_posts'));

        $this->page->setTemplate('posts/posts_list.twig');

        $this->page->addVars(array(
            'pagination' => $pager->pagination(),
            'user' => $this->app->user(),
            'postsList' => $pager->list(),
            'title' => 'Offres publiees | YannsJobs',
            'errors' => $pager->errors()
        ));
    }
    
    public function executeShow(HTTPRequest $request, array $errors = [])
    {
        $postId = (int)$request->getData('post');

        $member = $this->managers->getManagerOf('Member')->getSingle($this->app->user()->getAttribute('userId'));

        $post = $this->managers->getManagerOf('Post')->getSingle($postId);

        $interface = $this->app->checkContentAccess($post, $member);

        if ($member !== null){
            $post->setRecruiterName($this->managers->getManagerOf('Member')->getSingle($post->recruiterId())->username());
            $post->setApplied($this->managers->getManagerOf('Candidacy')->exists($member->id(), $post->id()));
            $post->setSaved($this->managers->getManagerOf('SavedPost')->exists($member->id(), $post->id()));
        }

        $this->page->setTemplate('posts/post.twig');

        $this->page->addVars(array(
            'user' => $this->app->user(),
            'interface' => $interface,
            'post' => $post,
            'title' => $post->title() . ' | YannsJobs',
            'errors'=> $errors
        ));
    }
    
}
