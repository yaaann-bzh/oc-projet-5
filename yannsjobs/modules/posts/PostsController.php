<?php
namespace yannsjobs\modules\posts;

use framework\Controller;
use entity\Post;
use framework\Pager;
use framework\HTTPRequest;
use framework\Form;


class PostsController extends Controller
{
    public function executeIndex(HTTPRequest $request)
    {
        $posts = $this->managers->getManagerOf('Post')->getList();
        $currentPage = (int)$request->getData('index');
        $maxPerPage = $this->app->config()->get('display', 'nb_posts');

        $pager = new Pager($this->app(), $posts);
        $pager->setListPagination($currentPage, $maxPerPage);
        $pager->setList();
        
        if ($this->app->user()->getAttribute('role') === 'candidate') {
            $candidate = $this->managers->getManagerOf('member')->getSingle($this->app->user()->getAttribute('userId'));
            foreach ($pager->list() as $post) {
                $post->setSaved(in_array($post->id(), $candidate->savedPosts()));
            }
        }

        foreach ($pager->list() as $post) {
            $post->setRecruiterName($this->managers->getManagerOf('Member')->getSingle($post->recruiterId())->username());
        }

        $this->page->setTemplate('home.twig');

        $this->page->addVars(array(
            'pagination' => $pager->pagination(),
            'user' => $this->app->user(),
            'postsList' => $pager->list(),
            'activePost' => $request->getData('post'),
            'title' => 'Accueil | YannsJobs',
            'errors' => $pager->errors()
        ));
    }
    
    public function executePublication(HTTPRequest $request)
    {
        $inputs = $this->app->config()->getFormConfig('inputs', ['title', 'location', 'duration', 'content']);
        $errors = [];
        $values = [];
        
        $form = new Form($inputs);

        if ($request->postExists('submit') AND $form->isValid($request)) {
            $values = $form->values();
            $values['recruiterId'] = (int)$this->app->user()->getAttribute('userId');
            
            try {
                $post = new Post($values);
                $this->managers->getManagerOf('Post')->add($post);

                return $this->app->httpResponse()->redirect('/recruiter/post-' . $post->id());
                
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        } else {
            $errors = $form->errors();
        }

        $this->page->setTemplate('redaction.twig');

        $this->page->addVars(array(
            'user' => $this->app->user(),
            'title' => 'Nouvelle offre | YannsJobs',
            'values' => $form->values(),
            'errors' => $errors
        ));
    }

    public function executeList(HTTPRequest $request) {
        
        $recruiter = $this->managers->getManagerOf('Member')->getSingle($this->app->user()->getAttribute('userId'));
        $filters['recruiterId'] = '=' . $recruiter->id();
        
        $posts = $this->managers->getManagerOf('Post')->getList($filters);
        $currentPage = (int)$request->getData('index');
        $maxPerPage = $this->app->config()->get('display', 'nb_posts');

        $pager = new Pager($this->app(), $posts);
        $pager->setListPagination($currentPage, $maxPerPage);
        $pager->setList();

        $this->page->setTemplate('posts_list.twig');

        $this->page->addVars(array(
            'pagination' => $pager->pagination(),
            'user' => $this->app->user(),
            'postsList' => $pager->list(),
            'title' => 'Offres publiees | YannsJobs',
            'errors' => $pager->errors()
        ));
    }
    
    public function executeShow(HTTPRequest $request)
    {
        $postId = (int)$request->getData('post');

        $recruiter = $this->managers->getManagerOf('Member')->getSingle($this->app->user()->getAttribute('userId'));

        $post = $this->managers->getManagerOf('Post')->getSingle($postId);
        
        $this->app->checkContentAccess($post, 'recruiter', $recruiter);
        
        $posts = $this->managers->getManagerOf('Post')->getList(array('recruiterId' => '=' . $recruiter->id()));
        $pager = new Pager($this->app(), $posts);
        $pager->setSinglePagination('post', $postId);

        $this->page->setTemplate('post.twig');

        $this->page->addVars(array(
            'pagination' => $pager->pagination(),
            'user' => $this->app->user(),
            'interface' => 'recruiter',
            'post' => $post,
            'title' => $post->title() . ' | YannsJobs',
            'errors' => $pager->errors()
        ));
    }

    public function executeUpdate(HTTPRequest $request)
    {
        
    }
}
