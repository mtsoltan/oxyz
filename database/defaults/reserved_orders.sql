-- We know that these are these because their product_id is 0.
INSERT INTO `orders` VALUES (1, 1, 0, 0, 0, '127.0.0.1', 0, '', 'Other', UNIX_TIMESTAMP(), 0);
INSERT INTO `orders` VALUES (2, 1, 0, 0, 0, '127.0.0.1', 0, '', 'Debt', UNIX_TIMESTAMP(), 0);
INSERT INTO `orders` VALUES (3, 1, 0, 0, 0, '127.0.0.1', 0, '', 'Project Fees', UNIX_TIMESTAMP(), 0);
INSERT INTO `orders` VALUES (4, 1, 0, 0, 0, '127.0.0.1', 0, '', 'Raw Material', UNIX_TIMESTAMP(), 0);
INSERT INTO `orders` VALUES (5, 1, 0, 0, 0, '127.0.0.1', 0, '', 'Tool Purchase', UNIX_TIMESTAMP(), 0);
INSERT INTO `orders` VALUES (6, 1, 0, 0, 0, '127.0.0.1', 0, '', 'Transportation', UNIX_TIMESTAMP(), 0);
INSERT INTO `orders` VALUES (7, 1, 0, 0, 0, '127.0.0.1', 0, '', 'Rent', UNIX_TIMESTAMP(), 0);
INSERT INTO `orders` VALUES (8, 1, 0, 0, 0, '127.0.0.1', 0, '', 'Utilities', UNIX_TIMESTAMP(), 0);
ALTER TABLE `orders` AUTO_INCREMENT = 20; -- Reserve first 20 orders IDs.
