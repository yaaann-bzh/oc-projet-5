<?php
namespace yannsjobs\modules\posts;

use framework\HTTPrequest;
use framework\Application;
use framework\Controller;
use framework\Manager;
use framework\Page;
use entity\Post;

class PostsController extends Controller
{
    public function executeIndex(HTTPRequest $request)
    {
        $nbPosts = $this->app->config()->get('display', 'nb_posts');
        $nbPages = (int)ceil($this->postManager->count() / $nbPosts);
        $this->page->addVars('nbPages', $nbPages);

        $exerptLength = $this->app->config()->get('display', 'nb_chars');

        $index = (int)$request->getData('index');
    
        if ($index === null OR $index === 0) {
            $index = 1;
        }

        if ($index === 1) {
            $prevIndex = '#';
            $nextIndex = 'index-' . ($index + 1);
            $begin = 0;
        } else {
            if ($index === $nbPages) {
                $prevIndex = 'index-' . ($index - 1);
                $nextIndex = '#';
            } else {
                $prevIndex = 'index-' . ($index - 1);
                $nextIndex = 'index-' . ($index + 1);
            }
            $begin = ($index - 1) * $nbPosts;
        }

        $this->page->addVars('index', $index);  
        $this->page->addVars('prevIndex', $prevIndex);
        $this->page->addVars('nextIndex', $nextIndex);

        $postsList = $this->postManager->getList($begin, $nbPosts);
        if ($request->getData('index') !== null AND empty($postsList)) {
            return $this->app->httpResponse()->redirect404();
        }
        $this->page->addVars('postsList', $postsList);

        $nbComments = [];
        $exerpts = [];
        foreach ($postsList as $post) {
            $filters['postId'] = '=' . $post->id();
            $nbComments[$post->id()] = $this->commentManager->count($filters);
            $exerpts[$post->id()] = $post->getExerpt($exerptLength);
        }
        $this->page->addVars('nbComments', $nbComments);
        $this->page->addVars('exerpts', $exerpts);

        $this->page->setTabTitle('Accueil');
        $this->page->setActiveNav('home');
        
        $this->page->setContent(__DIR__.'/view/index.php');
        $this->page->generate();
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

    public function executeRedaction(HTTPRequest $request)
    {
        if ($request->postExists('title') AND $request->postExists('content')) {
            $authorId = (int)$this->app->user()->getAttribute('id');
            $title = $request->postData('title');
            $content = $request->postData('content');

            try {
                $post = new Post([
                    'authorId' => $authorId,
                    'title' => $title,
                    'content' => $content
                ]);

                $this->postManager->add($post);
                return $this->app->httpResponse()->redirect('/post-' . $post->id());
                
            } catch (\Exception $e) {
                $message = $e->getMessage();
                $this->page->addVars('message', $message);
            }
        }

        $this->page->setTabTitle('Redaction');
        $this->page->setActiveNav('redaction');

        $this->page->setContent(__DIR__.'/view/redaction.php');
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
