<?php

namespace App\Models;

use PDO;

class Users extends DB
{
    public function getUser($email)
    {
        $this->connect();
        $sql = 'SELECT * FROM users WHERE email = :email LIMIT 1';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function addUser($array)
    {
        $this->connect();
        $sql = 'INSERT INTO users (email, name, phone) VALUES (:email, :name, :phone)';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($array);
        return $this->dbh->lastInsertId();
    }

    public function getAllUsers()
    {
        $this->connect();
        $sql = 'SELECT * FROM users WHERE 1';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
