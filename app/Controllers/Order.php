<?php

namespace App\Controllers;

use Exception;
use App\Models\Users;
use App\Models\Orders;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;

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
        if (!$userId) {
             $userId = $userModel->addUser([
                    'email' => $this->data['email'],
                    'name' => $this->data['name'],
                    'phone' => $this->data['phone']
                ]);
             $this->swiftMail($this->data['email'], $this->data['name']);
        }
        return $userId;
    }

    protected function sendMailToFile($userId, $orderId)
    {
        $count = $this->orderModel->getUsersCountOrders($userId);
        $fileShablon = ($count > 1) ?
            APPLICATION_PATH . 'Views/mails/mail_second_type.txt':
            APPLICATION_PATH . 'Views/mails/mail_first_type.txt';
        $mailShablon = file_get_contents($fileShablon);
        $mailShablon = str_replace('<idOrder>', $orderId, $mailShablon);
        $mailShablon = str_replace('<dateOrder>', $this->data['orderTime'], $mailShablon);
        $mailShablon = str_replace('<address>', $this->data['address'], $mailShablon);
        $mailShablon = str_replace('<count>', $count, $mailShablon);
        $mailShablon = str_replace('<date mail>', date('Y-m-d H-i-s'), $mailShablon);
        $file = APPLICATION_PATH . 'Views/mails/mail_orders.txt';
        return file_put_contents($file,$mailShablon . PHP_EOL . PHP_EOL, FILE_APPEND);
    }

    public function swiftMail($email, $name)
    {
        $transport = (new Swift_SmtpTransport(getenv('MAIL_HOST'), getenv('MAIL_PORT'), getenv('MAIL_SECURITY')))
            ->setUsername(getenv('MAIL_FROM_NAME'))
            ->setPassword(getenv('MAIL_FROM_PASSWORD'));

        $mailer = new Swift_Mailer($transport);

        $message = (new Swift_Message('send by Swift_Mailer'))
            ->setFrom([getenv('MAIL_FROM_NAME') => 'Бургерная: регистрация на сайте'])
            ->setTo([$email => $name])
            ->setBody('Вы успешно прошли регистрацию');

        $mailer->send($message);
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
        if ($this->sendMailToFile($userId, $orderId)) {
            echo 'ok';
        } else {
            throw new Exception('Письмо не отправлено');
        }
    }
}
