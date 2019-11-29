<?php
namespace framework;

abstract class Manager 
{
    public $dao;
    protected $entities;
    protected $table;

    public function __construct($dao)
    {
        $this->dao = $dao;
        $this->table = $this->getTable($this->entities);
    }

    private function getTable($entities)
    {
        $xml = new \DOMDocument;
        $xml->load(__DIR__.'/../../config/tables.xml');
        
        $elements = $xml->getElementsByTagName('define');
        $tables = [];

        foreach ($elements as $element)
        {
            $tables[$element->getAttribute('var')] = $element->getAttribute('value');
        }
        
        if (isset($tables[$entities]))
        {
            return $tables[$entities];
        }

        return null;
    }

    public abstract function getList($filters = [], $debut = null, $limit = null);

    public function count($filters=[])
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->table;

        if (!empty($filters)) {
            $sql .= ' WHERE ';
            foreach ($filters as $key => $filter) {
                $sql .= $key . $filter . ' AND ';
            }
            $sql = substr($sql, 0, -5);
        }
        return (int)$this->dao->query($sql)->fetchColumn();
    }
   
    
    public function getId($key, $var)
    {
        $sql = 'SELECT id FROM ' . $this->table . ' WHERE ' . $key . '="' . $var . '"';
        return $this->dao->query($sql)->fetchColumn();
    }
    
    public function getIdList(string $order = '', array $filters = [], array $search = []) {

        $sql = 'SELECT * FROM ' . $this->table;
        
        if (!empty($filters) || !empty($search)) {
            $sql .= ' WHERE ';
            foreach ($filters as $key => $filter) {
                $sql .= $key . $filter . ' AND ';
            }
            foreach ($search as $key => $value) {
                $sql .= 'LOWER(' . $key . ') REGEXP \'' . $value . '\' AND ';
            }
            $sql = substr($sql, 0, -5);
        }
           
        !empty($order) ? $sql .= ' ORDER BY ' . $order : null;

        $req = $this->dao->query($sql);
        $req->setFetchMode(\PDO::FETCH_ASSOC);

        $list = $req->fetchAll(\PDO::FETCH_COLUMN, 0);
        $req->closeCursor();
        
        return $list;  
    }

}
