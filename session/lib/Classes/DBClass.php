<?php
namespace Lib\Classes;


class DBClass
{
    private $dbName = null;
    private $dbUser = null;
    private $dbPass = null;
    private $dbConnect = null;

    function __construct($dbName, $dbUser, $dbPass)
    {
        $this->dbName = $dbName;
        $this->dbUser = $dbUser;
        $this->dbPass = $dbPass;

        try {
            $this->dbConnect = new \PDO('mysql:dbname=' . $this->dbName . ';host=localhost', $this->dbUser, $this->dbPass);
        } catch (\PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    function __destruct()
    {
        $this->dbConnect = null;
        return $this->dbConnect;
    }

    public function getConnection()
    {
        return $this->dbConnect;
    }
}
