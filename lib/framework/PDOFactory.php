<?php
namespace framework;

class PDOFactory
{
    public static function getMysqlConnexion(array $dbConnexion)
    {
        $dsn = 'mysql:host=' . $dbConnexion['db_host'] . ';dbname=' . $dbConnexion['db_name'];
        $db = new \PDO($dsn, $dbConnexion['db_user'], $dbConnexion['db_pass']);
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        return $db;
    }
}