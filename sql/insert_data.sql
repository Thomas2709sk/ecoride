
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

INSERT INTO `carpools` (`id`, `driver_id`, `car_id`, `day`, `begin`, `end`, `address_start`, `address_end`, `places_available`, `price`, `status`, `carpool_number`, `is_ecological`, `startLat`, `startLon`, `endLat`, `endLon`, `duration`) VALUES
(3, 13, 15, '2025-06-30', '08:00:00', '11:45:00', '7 rue d\'Estienne d\'Orves 94381 Bonneuil-sur-Marne', '263 avenue Général Leclerc 35042 RENNES', 2, 15, 'Confirmé', 'COV#eed700c8', 0, NULL, NULL, NULL, NULL, NULL),
(6, 13, 15, '2025-06-23', '14:00:00', '15:40:00', 'Porte Maillot, 75017 Paris', 'Gare de Rouen  76000 Rouen', 2, 12, 'Confirmé', 'COV#153462b6', 0, NULL, NULL, NULL, NULL, NULL),
(11, 14, 16, '2025-07-16', '18:00:00', '18:38:00', '7 rue d\'Estienne d\'Orves 94381 Bonneuil-sur-Marne', '95700 Roissy-en-France', 3, 5, 'Terminé', 'COV#30439e17', 1, 48.7743, 2.4875, 49.0036, 2.51698, 2285),
(12, 13, 14, '2025-07-16', '19:00:00', '23:37:00', '7 rue d\'Estienne d\'Orves 94381 Bonneuil-sur-Marne', 'Place Bellecour, 69002 Lyon', 1, 4, 'Confirmé', 'COV#702aa067', 1, 48.7743, 2.4875, 45.7568, 4.83152, 16662),
(14, 13, 14, '2025-08-07', '07:00:00', '09:50:00', '34 bis Rue des Bouchers, 59800, Lille France', '8 rue Beaurepaire 75010 Paris', 2, 10, 'A venir', 'COV#eb083937', 1, 50.6379, 3.05827, 48.8695, 2.36363, 10215),
(15, 14, 16, '2025-08-07', '10:00:00', '12:45:00', 'Lille', 'Paris', 3, 10, 'A venir', 'COV#d6ab80d7', 1, 50.6244, 3.06786, 48.8575, 2.35138, 9917);

INSERT INTO `carpools_users` (`carpools_id`, `users_id`, `is_confirmed`, `is_ended`, `is_answered`) VALUES
(3, 3, 1, 1, 1),
(6, 7, 1, 1, 1),
(12, 3, 1, 1, 1);