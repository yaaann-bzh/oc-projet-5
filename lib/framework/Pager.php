<?php

namespace framework;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class Pager extends ApplicationComponent
{
    protected $adapter;
    protected $pagerfanta;
    protected $pagination;
    protected $list;
    protected $errors = [];

    public function __construct(Application $app, $entities, int $currentPage, $maxPerPage)
    {
        parent::__construct($app);

        $this->adapter = new ArrayAdapter($entities);
        $this->pagerfanta = new Pagerfanta($this->adapter);
        $this->setListPagination($currentPage, $maxPerPage);
    }

    public function setListPagination($currentPage, $maxPerPage)
    {
        $this->pagerfanta->setMaxPerPage($maxPerPage);
        if ($this->pagerfanta->haveToPaginate()) {
            $this->pagination = array(
                'current' => 1,
                'previous' => '#',
                'next' => '#',
                'total' => $this->pagerfanta->getNbPages()
            );

            if ((int)$currentPage !== 0) {
                try {
                    $this->pagerfanta->setCurrentPage($currentPage);
                    $this->pagination['current'] = $this->pagerfanta->getCurrentPage();
                } catch (\Exception $e) {
                    $this->errors[] = $e->getMessage();
                }
            }

            if ($this->pagerfanta->hasPreviousPage()) {
                $this->pagination['previous'] = '/index-'. $this->pagerfanta->getPreviousPage();
            }
            if ($this->pagerfanta->hasNextPage()) {
                $this->pagination['next'] = '/index-'. $this->pagerfanta->getNextPage();
            }
        }
        
        $this->setList();
    }
    
    public function getEntities(Manager $manager) {
        $list = [];
        foreach ($this->list() as $postId) {
            $entity = $manager->getSingle($postId);
            !is_null($entity) ? $list[] = $entity : null;
        }  
        return $list;
    }

    public function setList()
    {
        $this->list = $this->pagerfanta->getCurrentPageResults();
    }

    public function errors()
    {
        return $this->errors;
    }

    public function list()
    {
        return $this->list;
    }

    public function pagination()
    {
        return $this->pagination;
    }
}