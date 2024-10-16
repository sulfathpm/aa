CREATE TABLE `order_assignments` (
  `ASSIGNMENT_ID` int(11) NOT NULL AUTO_INCREMENT,
  `ORDER_ID` int(11) NOT NULL,
  `STAFF_ID` int(11) NOT NULL,
  `ASSIGNED_AT` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ASSIGNMENT_ID`),
  FOREIGN KEY (`ORDER_ID`) REFERENCES `orders` (`ORDER_ID`),
  FOREIGN KEY (`STAFF_ID`) REFERENCES `users` (`USER_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
