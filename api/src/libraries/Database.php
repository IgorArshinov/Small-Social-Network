<?php

namespace libraries;

use PDO;

class Database
{
    private $pdo;
    private $error;

    public function __construct()
    {
        $config = parse_ini_file('../api/src/config/config.ini');
        $dataSourceName = 'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'];
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        try {
            $this->pdo = new PDO($dataSourceName, $config['username'], $config['password'], $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }

    public function getPDO()
    {
        return $this->pdo;
    }
}
