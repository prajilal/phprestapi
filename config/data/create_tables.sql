CREATE TABLE `product` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(150) NOT NULL UNIQUE KEY,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `purchaser` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(150) NOT NULL UNIQUE KEY,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `purchaser_product` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `purchaser_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `purchase_timestamp` varchar(50) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;