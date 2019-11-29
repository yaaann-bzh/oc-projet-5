<?php


namespace framework;


class Search {
    protected $filters = [];
    protected $search = [];

    public function __construct(array $search) {
        foreach ($search as $key => $value) {
            $this->setSearch($key, $value);
        }
    }
        
    public function setFilter($key, $value) {
        $this->filters[$key] = $value;
    }
    
    public function setSearch($key, $value) {
        if ($value !== null) {
            $this->search[$key] = strtolower($value);
        }    
    }
    
    public function getFilter($key) {
        return $this->filters[$key];
    }
    
    public function filters() {
        return $this->filters;
    }
    
    public function search() {
        return $this->search;
    }
    
}
