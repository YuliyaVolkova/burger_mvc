<?php

namespace App\Models;

use PDO;

class Orders extends DB
{
    public function addOrder($array)
    {
        $this->connect();
        $sql = 'INSERT INTO orders (userId, dateOrder, shippingAddress, typePayment, callback, comments)
    VALUES (:userId, :dateOrder, :shippingAddress, :typePayment, :callback, :comments)';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($array);
        return $this->dbh->lastInsertId();
    }

    public function getAllOrders()
    {
        $this->connect();
        $sql = 'SELECT * FROM orders WHERE 1';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getUsersCountOrders($userId)
    {
        $this->connect();
        $sql = 'SELECT userId FROM orders WHERE userId = :userId';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        return $stmt->rowCount();
    }
}
