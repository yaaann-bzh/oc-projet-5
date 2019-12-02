<?php


namespace framework;


class Search {
    protected $filters = [];
    protected $search = [];
    protected $charsReplace = array(
        'é' => 'e',
        'è' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ç' => 'c',
        'à' => 'a',
        'â' => 'a',
        'ä' => 'a',
        'î' => 'i',
        'ï' => 'i'
        );

    public function __construct(array $search) {
        foreach ($search as $key => $value) {
            $this->setSearch($key, $value);
        }
    }
        
    public function setFilter($key, $value) {
        $this->filters[$key] = $value;
    }
    
    public function setSearch($key, $value) {
        if (!empty($value)) {
            $search = array_keys($this->charsReplace);
            $this->search[$key] = strtolower(str_ireplace($search, $this->charsReplace, $value));
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
    
    public function charsReplace() {
        return $this->charsReplace;
    }
    
}
