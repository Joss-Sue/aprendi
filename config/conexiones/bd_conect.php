<?php

class Dbconfig {
    protected $serverName;
    protected $userName;
    protected $password;
    protected $dbName;

    function Dbconfig() {
        $this -> serverName = 'localhost';
        $this -> userName = 'root';
        $this -> passCode = 'pass';
        $this -> dbName = 'dbase';
    }
}


class DatabaseConnection extends Dbconfig
{
    protected $hostName;
    protected $userName;
    protected $password;
    protected $databaseName;

    public $connectionString;
    public $dataSet;
    private $sqlQuery;

    public function __construct($host, $username, $password, $database)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->connect();
    }

    

    function Mysql() {
        $this -> connectionString = NULL;
        $this -> sqlQuery = NULL;
        $this -> dataSet = NULL;

        $dbPara = new Dbconfig();
        $this -> databaseName = $dbPara -> dbName;
        $this -> hostName = $dbPara -> serverName;
        $this -> userName = $dbPara -> userName;
        $this -> passCode = $dbPara ->passCode;
        $dbPara = NULL;
    }
  
    function dbConnect()    {
        $this ->connectionString = mysqli_connect($this->serverName, $this->userName, $this->password);
        mysqli_select_db($this->databaseName,$this->connectionString);
        return $this->connectionString;
    }

    }
    ?>