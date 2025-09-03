-- Active: 1756370426129@@127.0.0.1@3307@ecoride_db
-- Active: 1744105202078@@127.0.0.1@3306
CREATE DATABASE IF NOT EXISTS `ecoride_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `ecoride_db`;

CREATE TABLE IF NOT EXISTS `users` (
    `user_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `firstname` VARCHAR(50) NOT NULL,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `address` VARCHAR(255),
    `phone` VARCHAR(15),
    `date_of_birth` DATE,
    `photo` BLOB,
    `credits` INT DEFAULT 20,
    `rating` FLOAT DEFAULT 0,
    `status` ENUM('active', 'banned') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `appreciation_nb` INT DEFAULT 0,
    CONSTRAINT chk_credits CHECK (credits >= 0),
    CONSTRAINT chk_rating CHECK (
        rating >= 0
        AND rating <= 5
    )
);

CREATE TABLE IF NOT EXISTS `roles` (
    `role_id` INT AUTO_INCREMENT PRIMARY KEY,
    `role_name` ENUM('user', 'admin', 'employee') NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS `user_roles` (
    `user_role_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `role_id` INT NOT NULL,
    `user_role` ENUM('driver', 'passenger', 'both'),
    `assigned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_user_role` (`user_id`, `role_id`)
);

CREATE TABLE IF NOT EXISTS `brands` (
    `brand_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name_brand` VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS `vehicles` (
    `vehicle_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `brand_id` INT NOT NULL,
    `model` VARCHAR(50) NOT NULL,
    `registration_number` VARCHAR(20) NOT NULL UNIQUE,
    `first_registration_date` DATE NOT NULL,
    `color` VARCHAR(30),
    `seats_available` INT NOT NULL DEFAULT 1,
    `energy_type` ENUM(
        'electric',
        'hybrid',
        'diesel',
        'essence'
    ) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_seats_available CHECK (
        seats_available > 0
        AND seats_available <= 6
    ),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS `drivers_preferences` (
    `preference_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `smoking_allowed` BOOLEAN DEFAULT FALSE,
    `animals_allowed` BOOLEAN DEFAULT FALSE,
    `personalized_preferences` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_user_preference` (`user_id`)
);

CREATE TABLE IF NOT EXISTS `carpools` (
    `carpool_id` INT AUTO_INCREMENT PRIMARY KEY,
    `driver_id` INT NOT NULL,
    `vehicle_id` INT NOT NULL,
    `departure` VARCHAR(255) NOT NULL,
    `arrival` VARCHAR(255) NOT NULL,
    `departure_time` DATETIME NOT NULL,
    `arrival_time` DATETIME NOT NULL,
    `seats_available` INT NOT NULL,
    `price_per_seat` DECIMAL(10, 2) NOT NULL,
    `is_ecological` BOOLEAN DEFAULT TRUE,
    `status` ENUM(
        'scheduled',
        'in progress',
        'finished',
        'canceled'
    ) DEFAULT 'scheduled',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`driver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`) ON DELETE CASCADE,
    CONSTRAINT chk_seats_available_carpool CHECK (seats_available > 0),
    CONSTRAINT chk_price_per_seat CHECK (price_per_seat > 0),
    CONSTRAINT chk_times CHECK (arrival_time > departure_time)
);

CREATE TABLE IF NOT EXISTS `reservation` (
    `reservation_id` INT AUTO_INCREMENT PRIMARY KEY,
    `carpool_id` INT NOT NULL,
    `passenger_id` INT NOT NULL,
    `seats_booked` INT NOT NULL,
    `status` ENUM(
        'pending',
        'confirmed',
        'canceled',
        'completed'
    ) DEFAULT 'pending',
    `amount_paid` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `cancellation_date` TIMESTAMP NULL DEFAULT NULL,
    `confirmation_passenger` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (`carpool_id`) REFERENCES `carpools` (`carpool_id`) ON DELETE CASCADE,
    FOREIGN KEY (`passenger_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
    CONSTRAINT chk_seats_booked CHECK (seats_booked > 0),
    CONSTRAINT chk_amount_paid CHECK (amount_paid > 0),
    UNIQUE KEY `unique_passenger_carpool` (`carpool_id`, `passenger_id`)
);

CREATE TABLE IF NOT EXISTS `transactions` (
    `transaction_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `reservation_id` INT NULL,
    `amount` INT NOT NULL,
    `transaction_type` ENUM('credit', 'debit') NOT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`reservation_id`) REFERENCES `reservation` (`reservation_id`) ON DELETE SET NULL
);

--Insertion des rôles par défaut
INSERT INTO
    `roles` (`role_name`)
VALUES ('user'),
    ('admin'),
    ('employee');

--Insertion des marques par défaut
INSERT INTO
    `brands` (`name_brand`)
VALUES ('Toyota'),
    ('Ford'),
    ('Honda'),
    ('Peugeot'),
    ('Renault'),
    ('Citroën '),
    ('BMW'),
    ('Mercedes-Benz'),
    ('Volkswagen'),
    ('Audi'),
    ('Tesla'),
    ('Hyundai'),
    ('Kia');