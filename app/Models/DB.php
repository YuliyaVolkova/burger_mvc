<?php

namespace App\Models;

use PDO;

class DB
{
    protected $dbh = null;

    public function connect()
    {
        if (!$this->dbh) {
            $dsh = "mysql:host=" . getenv('DB_HOST_NAME') . ";dbname=" . getenv('DB') . ";charset=utf8";
            $this->dbh = new PDO($dsh, getenv('DB_USER_NAME'), getenv('DB_PASSWORD'));
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }
}
