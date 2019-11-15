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
        $pager->setPagination($currentPage, $maxPerPage);
        $pager->setList();

        foreach ($pager->list() as $post) {
            $post->setRecruiterName($this->managers->getManagerOf('Member')->getSingle($post->recruiterId())->username());
        }

        $this->page->setTemplate('home.twig');

        $this->page->addVars(array(
            'pagination' => $pager->pagination(),
            'user' => $this->app->user(),
            'postsList' => $pager->list(),
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

    public function executeShow(HTTPRequest $request)
    {
        $id = (int)$request->getData('id');
        $post = $this->postManager->getSingle($id);

        if (empty($post)) {
            return $this->app->httpResponse()->redirect404();
        }

        $author = $this->memberManager->getSingle($post->authorId());

        $filters['postId'] = '=' . $id;
        $comments = $this->commentManager->getList(null, null, $filters);
        
        $members = [];
        foreach ($comments as $comment ) {
            $members[$comment->id()] = $this->memberManager->getSingle($comment->memberId());
        }
        
        $pagination = [];
        $pagination['total'] = $this->postManager->count();
        $postsIdList = $this->postManager->getIdList();

        $rank = array_search((int)$post->id(), $postsIdList) + 1;
        
            switch ($rank) {
                case 1:
                    $pagination['nextLink'] = 'post-' . $postsIdList[$rank];
                    $pagination['current'] = $rank;
                    $pagination['prevLink'] = '#';
                break;

                case count($postsIdList):
                    $pagination['nextLink'] = '#';
                    $pagination['current'] = $rank;
                    $pagination['prevLink'] = 'post-' . $postsIdList[$rank - 2];
                break;
                
                default:
                    $pagination['nextLink'] = 'post-' . $postsIdList[$rank];
                    $pagination['current'] = $rank;
                    $pagination['prevLink'] = 'post-' . $postsIdList[$rank - 2];
                break;
            }
               
        $this->page->addVars('pagination', $pagination);

        $updated = $request->getData('updated');
        $this->page->addvars('updated', $updated);

        $this->page->addVars('post', $post);
        $this->page->addVars('author', $author);
        $this->page->addVars('comments', $comments);
        $this->page->addVars('members', $members);

        $this->page->setTabTitle($post->title());

        $this->page->setContent(__DIR__.'/view/single.php');
        $this->page->generate();
    }

    public function executeUpdate(HTTPRequest $request)
    {
        $postId = (int)$request->getData('post');
        $post = $this->postManager->getSingle($postId);

        if (empty($post)) {
            return $this->app->httpResponse()->redirect404();
        }

        if ($request->postExists('action')) {
            try {
                switch ($request->postData('action')) { 
                    case 'Modifier': 
                        $title = $request->postData('title');
                        $content = $request->postData('content');
                        $this->postManager->update($post->id(), $title, $content);
                        return $this->app->httpResponse()->redirect('/post-' . $postId . '-updated');
                    break;

                    case 'Supprimer': 
                        $this->postManager->delete($post->id());
                        $this->commentManager->deleteFromPost($post->id());
                        return $this->app->httpResponse()->redirect('/');
                    break;
                }

            } catch (\Exception $e) {
                $intro = 'Erreur lors de la modification de la publication';
                $message = $e->getMessage();
            }

            $this->errorPage($intro, $message);
        }

        $this->page->addVars('post', $post);

        $this->page->setTabTitle('Edition');
        $this->page->setActiveNav('redaction');

        $this->page->setContent(__DIR__.'/view/redaction.php');
        $this->page->generate();
    }
}
