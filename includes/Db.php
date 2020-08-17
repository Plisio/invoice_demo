<?php


class Db
{
    protected $pdo;

    public function __construct($dsn, $username, $password, $options = []){
        $this->pdo = new PDO($dsn, $username, $password, $options);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function close(){
        $this->pdo = null;
    }
}