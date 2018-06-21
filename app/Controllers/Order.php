<?php

namespace App\Controllers;

use Exception;
use App\Models\Users;
use App\Models\Orders;

class Order
{
    protected $data;
    protected $orderModel;

    protected function clearAll($data)
    {
        $data = strip_tags($data);
        $data = htmlspecialchars($data, ENT_QUOTES);
        $data = trim($data);
        return $data;
    }

    protected function getData()
    {
        $data = array_map($this->clearAll, $_POST);
        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->data = [
                'email' => strtolower($data['email']),
                'name' => $data['name'],
                'phone' => $data['phone'],
                'orderTime' => date('Y-m-d H-i-s'),
                'address' => 'ул. ' . $data['street'] . '  д. ' . $data['home'] . '/' . $data['part'] . ' кв. ' . $data['appt'] . ' эт. ' . $data['floor'],
                'payment' => ($data['payment'] === 'card') ? 'КАРТОЙ' : 'НАЛИЧНЫМИ',
                'callback' => ($data['callback'] === 'on') ? 'НЕ ПЕРЕЗВАНИВАТЬ' : 'МОЖНО ПЕРЕЗВАНИВАТЬ',
                'comments' => $data['comment']
            ];
        } elseif (!strlen($data['email'])) {
            throw new Exception('Не указан e-mail');
        } else {
            throw new Exception(('Не корректно указан e-mail'));
        }
    }

    protected function checkUser()
    {
        $userModel = new Users();
        $userId = $userModel->getUser($this->data['email'])->id;
        if($userId) {
            return $userId;
        } else {
            return $userModel->addUser([
                    'email' => $this->data['email'],
                    'name' => $this->data['name'],
                    'phone' => $this->data['phone']
            ]);
        }
    }

    protected function sendMail($userId, $orderId)
    {
        $count = $this->orderModel->getUsersCountOrders($userId);
        $mailShablon = ($count === 1) ?
            file_get_contents(APPLICATION_PATH . 'Views/mails/mail_first_type.txt') :
            file_get_contents(APPLICATION_PATH . 'Views/mails/mail_second_type.txt');
        $mailShablon = str_replace('<idOrder>', $orderId, $mailShablon);
        $mailShablon = str_replace('<dateOrder>', $this->data['orderTime'], $mailShablon);
        $mailShablon = str_replace('<address>', $this->data['address'], $mailShablon);
        $mailShablon = str_replace('<count>', $count, $mailShablon);
        $mailShablon = str_replace('<date mail>', date('Y-m-d H-i-s'), $mailShablon);
        $file = APPLICATION_PATH . 'Views/mails/mail_orders.txt';
        return file_put_contents($file,$mailShablon . PHP_EOL . PHP_EOL, FILE_APPEND);
    }

    public function __construct()
    {
        $this->getData();
        $userId = $this->checkUser();
        $this->orderModel = new Orders();
        $orderId = $this->orderModel->addOrder([
                'userId' => $userId,
                'dateOrder' => $this->data['orderTime'],
                'shippingAddress' => $this->data['address'],
                'typePayment' => $this->data['payment'],
                'callback' => $this->data['callback'],
                'comments' => $this->data['comments']
            ]);
        if ($this->sendMail($userId, $orderId)) {
            echo 'ok';
        } else {
            throw new Exception('Письмо не отправлено');
        }
    }
}
