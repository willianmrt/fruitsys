-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.13-MariaDB


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


--
-- Create schema agregue
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ agregue;
USE agregue;

--
-- Table structure for table `agregue`.`order_detail`
--

DROP TABLE IF EXISTS `order_detail`;
CREATE TABLE `order_detail` (
  `id_order` int(10) unsigned NOT NULL DEFAULT 0,
  `id_product` int(10) unsigned NOT NULL DEFAULT 0,
  `product_price` double DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `subtotal` double DEFAULT NULL,
  PRIMARY KEY (`id_order`,`id_product`),
  KEY `FK_product` (`id_product`),
  CONSTRAINT `FK_order` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id`),
  CONSTRAINT `FK_product` FOREIGN KEY (`id_product`) REFERENCES `product` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `agregue`.`order_detail`
--

/*!40000 ALTER TABLE `order_detail` DISABLE KEYS */;
INSERT INTO `order_detail` (`id_order`,`id_product`,`product_price`,`amount`,`subtotal`) VALUES 
 (1,2,7.85,10,78.5),
 (1,3,2.99,3,8.97),
 (2,1,15.5,5,77.5),
 (2,2,7.85,2,15.7),
 (2,5,12.8,5,64),
 (3,2,7.85,5,39.25),
 (3,3,5.5,5,27.5),
 (3,5,12.8,5,64);
/*!40000 ALTER TABLE `order_detail` ENABLE KEYS */;


--
-- Table structure for table `agregue`.`orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `registration_date` date DEFAULT NULL,
  `payment` int(10) unsigned DEFAULT NULL,
  `zip_code` varchar(9) NOT NULL DEFAULT '',
  `address` varchar(60) NOT NULL DEFAULT '',
  `district` varchar(60) NOT NULL DEFAULT '',
  `number` varchar(10) NOT NULL DEFAULT '',
  `city` varchar(45) NOT NULL DEFAULT '',
  `state` varchar(45) NOT NULL DEFAULT '',
  `total` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `agregue`.`orders`
--

/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` (`id`,`registration_date`,`payment`,`zip_code`,`address`,`district`,`number`,`city`,`state`,`total`) VALUES 
 (1,'2020-09-10',2,'86182400','Rua Mathias Hein','Jardim Santo Antônio','10A','Cambé','PR',87.47),
 (2,'2020-09-10',2,'86182000','Rua Noruega','Centro','100','Cambé','PR',157.2),
 (3,'2020-09-10',1,'86182100','Rua Cuiabá','Vila Guarani','200','Cambé','PR',130.75);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;


--
-- Table structure for table `agregue`.`product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `use_by` date DEFAULT NULL,
  `price` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `agregue`.`product`
--

/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` (`id`,`title`,`use_by`,`price`) VALUES 
 (1,'manga','2020-09-01',20),
 (2,'maracujá','2020-09-21',7.85),
 (3,'laranja','2020-09-13',5.5),
 (4,'ameixa','2020-10-31',8.5),
 (5,'uva','2020-10-09',12.8),
 (6,'banana','2020-10-01',5.6),
 (7,'maçã','2020-10-31',14.9),
 (9,'tangerina','2020-10-15',8.85),
 (10,'limão','2020-09-16',7.99);
/*!40000 ALTER TABLE `product` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
