
-- To add Users into table
INSERT INTO `users` (`id`, `email`, `roles`, `password`, `pseudo`, `photo`, `credits`, `is_verified`, `is_passenger`) VALUES
(1, 'admin@mail.fr', '[\"ROLE_ADMIN\"]', '$2y$13$QC31qzvsuWBpHjkad.mvvem7bW7sP9okWpf0yrBqwdtI4LtrA7aNm', 'Jose', NULL, 26, 1, 1),
(3, 'passager@mail.fr', '[]', '$2y$13$2KxgxdKY88CKfuJdfFhaHuqWoD/70kDJnIpbzN9.fMbFVuvOsWosu', 'passager', NULL, 15, 0, 1),
(4, 'chauffeur@mail.fr', '[\"ROLE_USER\",\"ROLE_DRIVER\"]', '$2y$13$6mTnzrMAsVgJU6/Evw7jS.olFbrqzFIbyEWrjXBUXvg3iKqPwzZuS', 'chauffeur', '21714d9db526de60889fc1050e0fb8f4.webp', 43, 0, 0),
(7, 'chaufpass@mail.fr', '[\"ROLE_USER\",\"ROLE_DRIVER\"]', '$2y$13$Vj7fFWt.sCqGLY5M.6QzGO2v61CjxnR544HWLNOWSMVrzc4e7Wh5O', 'chaufpass', '9547f59f5a1e91149472621353ddf38e.webp', 8, 1, 1),
(8, 'employe@mail.fr', '[\"ROLE_STAFF\"]', '$2y$13$goNJ6lT8/38Ii96n59UMt.YNhxTscv4GoMuDd2JzEz6iw7W7f6KRe', 'employe', NULL, 20, 1, 1);

INSERT INTO `cars` (`id`, `driver_id`, `brand`, `model`, `color`, `seats`, `plate_number`, `first_registration`, `energy`) VALUES
(14, 13, 'Porsche', 'Taycan', 'jaune', 2, 'JP 279 SK', '2020-02-03', 'Electrique'),
(15, 13, 'Renault', 'Clio', 'Blanche', 3, 'AB 123 CD', '2014-07-01', 'Essence'),
(16, 14, 'Peugeot', 'e-208', 'vert', 3, 'eb 208 pb', '2019-11-18', 'Electrique');

INSERT INTO `drivers` (`id`, `user_id`, `animals`, `smoking`, `preferences`) VALUES
(13, 4, 0, 0, 'Pas d\'enfants'),
(14, 7, 1, 1, 'peux faire un détour si proche de l\'adresse');