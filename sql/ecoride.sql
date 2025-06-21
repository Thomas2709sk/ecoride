-- This file only contains the creation of the DB and the table
CREATE DATABASE IF NOT EXISTS ecoride;

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(30) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    credits INT DEFAULT 20,
    roles LONGTEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS drivers (
    driver_id INT AUTO_INCREMENT PRIMARY KEY,
    animals TINYINT(1) NOT NULL,
    smoking TINYINT (1) NOT NULL,
    preferences VARCHAR(255) NULL,
    user_id INT NOT NULL,
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS cars (
    car_id INT AUTO_INCREMENT PRIMARY KEY,
    brand VARCHAR(30) NOT NULL,
    model VARCHAR(30) NOT NULL,
    color VARCHAR(30) NOT NULL,
    seats INT NOT NULL,
    plate_number VARCHAR(15) NOT NULL,
    first_registration DATE NOT NULL,
    driver_id INT NOT NULL,
    CONSTRAINT fk_driver FOREIGN KEY (driver_id) REFERENCES drivers(driver_id)
);

CREATE TABLE IF NOT EXISTS carpools (
    carpool_id INT AUTO_INCREMENT PRIMARY KEY,
    day DATE NOT NULL,
    begin TIME NOT NULL,
    end TIME NOT NULL,
    address_start VARCHAR(255) NOT NULL,
    address_end VARCHAR(255) NOT NULL,
    price INT NOT NULL,
    status ENUM('A venir', 'En cours', 'Terminé', 'Confirmé', 'Vérification par la plateforme') NOT NULL DEFAULT 'A venir',
    carpool_number VARCHAR(255) NOT NULL,
    driver_id INT NOT NULL,
    car_id INT NOT NULL,
    CONSTRAINT fk_carpool_driver FOREIGN KEY (driver_id) REFERENCES drivers(driver_id)
    CONSTRAINT fk_carpool_car FOREIGN KEY (car_id) REFERENCES cars(cars_id)
);

CREATE TABLE IF NOT EXISTS users_carpools (
    user_id INT NOT NULL,
    carpool_id INT NOT NULL,
    PRIMARY KEY (user_id, carpool_id),
    CONSTRAINT fk_users_carpools_user FOREIGN KEY (user_id) REFERENCES users(user_id),
    CONSTRAINT fk_users_carpools_carpool FOREIGN KEY (carpool_id) REFERENCES carpools(carpool_id)
);

CREATE TABLE IF NOT EXISTS reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    commentary TEXT NOT NULL,
    validate TINYINT(1) NOT NULL,
    rate INT NOT NULL,
    user_id INT NOT NULL,
    driver_id INT NOT NULL,
    carpool_id INT NOT NULL,
    CONSTRAINT fk_review_user FOREIGN KEY (user_id) REFERENCES users(user_id),
    CONSTRAINT fk_review_driver FOREIGN KEY (driver_id) REFERENCES drivers(driver_id),
    CONSTRAINT fk_review_carpool FOREIGN KEY (carpool_id) REFERENCES carpools(carpool_id)
);

ALTER TABLE Users
ADD COLUMN is_passenger TINYINT(1) NOT NULL DEFAULT 1;

ALTER TABLE Carpools
ADD COLUMN is_ecological TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE Cars
ADD COLUMN energy VARCHAR(15) NOT NULL;

ALTER TABLE carpools_users
ADD COLUMN isConfirmed TINYINT(1) NOT NULL DEFAULT 0;