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
    protected $errors;

    public function __construct(Application $app, $entities)
    {
        parent::__construct($app);

        $this->adapter = new ArrayAdapter($entities);
        $this->pagerfanta = new Pagerfanta($this->adapter);
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
    }
    
    public function setSinglePagination(string $entity, $id, $maxPerPage = 1) {

        $this->pagerfanta->setMaxPerPage($maxPerPage);
        if ($this->pagerfanta->haveToPaginate()) {
            $this->pagination = array(
                'previous' => '#',
                'next' => '#',
                'total' => $this->pagerfanta->getNbPages()
            );

            if ((int)$id !== 0) {
                try {
                    foreach ($this->adapter->getArray() as $key => $object) {
                        if ((int)$object->id() === $id) {
                            $this->pagerfanta->setCurrentPage($key + 1);
                        }
                    }
                    $this->pagination['current'] = $this->pagerfanta->getCurrentPage();
                } catch (\Exception $e) {
                    $this->errors[] = $e->getMessage();
                }
            }

            if ($this->pagerfanta->hasPreviousPage()) {
                $this->pagination['previous'] = $entity . '-' . $this->adapter->getArray()[$this->pagerfanta->getCurrentPage() - 2]->id();
            }
            if ($this->pagerfanta->hasNextPage()) {
                $this->pagination['next'] = $entity . '-' . $this->adapter->getArray()[$this->pagerfanta->getCurrentPage()]->id();
            }
        }
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