<?php
namespace framework;

class Managers
{
    protected $api = null;
    protected $dao = null;
    protected $managers = [];

    public function __construct($dao)
    {
        $this->dao = $dao;
    }

    public function getManagerOf($entity)
    {
        if (!is_string($entity) || empty($entity))
        {
            throw new \InvalidArgumentException('L\'entité spécifié est invalide');
        }

        if (!isset($this->managers[$entity]))
        {
            $manager = '\\model\\'.$entity.'Manager';

            $this->managers[$entity] = new $manager($this->dao);
        }

        return $this->managers[$entity];
    }
}