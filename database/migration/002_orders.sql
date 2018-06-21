CREATE TABLE `orders` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` int(11) UNSIGNED NOT NULL,
  `dateOrder` timestamp NOT NULL,
  `shippingAddress` varchar(255) NOT NULL,
  `typePayment` enum('НАЛИЧНЫМИ','КАРТОЙ') NOT NULL,
  `callback` enum('НЕ ПЕРЕЗВАНИВАТЬ','МОЖНО ПЕРЕЗВАНИВАТЬ') NOT NULL,
  `comments` tinytext,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`userId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;