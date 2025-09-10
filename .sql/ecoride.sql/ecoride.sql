-- Active: 1756370426129@@127.0.0.1@3307@ecoride_db
DROP DATABASE IF EXISTS `ecoride_db`;

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
    `photo` VARCHAR(255),
    `credits` INT DEFAULT 20,
    `rating` FLOAT DEFAULT 0,
    `status` ENUM('active', 'banned') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
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
    `is_ecological` BOOLEAN DEFAULT FALSE,
    `status` ENUM(
        'scheduled',
        'in_progress',
        'finished',
        'canceled'
    ) DEFAULT 'scheduled',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`driver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`) ON DELETE CASCADE,
    CONSTRAINT chk_seats_available_carpool CHECK (seats_available > 0),
    CONSTRAINT chk_price_per_seat CHECK (price_per_seat > 0),
    CONSTRAINT chk_times CHECK (arrival_time > departure_time),
    INDEX idx_departure_time (`departure_time`),
    INDEX idx_departure (`departure`),
    INDEX idx_arrival (`arrival`)
);

CREATE TABLE IF NOT EXISTS `reservation` (
    `reservation_id` INT AUTO_INCREMENT PRIMARY KEY,
    `carpool_id` INT NOT NULL,
    `passenger_id` INT NOT NULL,
    `seats_booked` INT NOT NULL,
    `status` ENUM(
        'confirmed',
        'canceled',
        'completed',
        'awaiting_passenger_confirmation',
        'disputed'
    ) DEFAULT 'confirmed',
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

-- Insertion des rôles par défaut
INSERT INTO
    `roles` (`role_name`)
VALUES ('user'),
    ('admin'),
    ('employee');

-- Insertion des marques par défaut
INSERT INTO
    `brands` (`name_brand`)
VALUES ('Toyota'),
    ('Ford'),
    ('Honda'),
    ('Peugeot'),
    ('Renault'),
    ('Citroën'),
    ('BMW'),
    ('Mercedes-Benz'),
    ('Volkswagen'),
    ('Audi'),
    ('Tesla'),
    ('Hyundai'),
    ('Kia');

ALTER TABLE reservation ADD COLUMN complaint_comment TEXT NULL;

-- Insertion des utilisateurs fictifs
INSERT INTO
    `users` (
        `name`,
        `firstname`,
        `username`,
        `email`,
        `password_hash`,
        `address`,
        `phone`,
        `date_of_birth`,
        `status`
    )
VALUES (
        'Dupont',
        'Marie',
        'marie.dupont',
        'marie.dupont@email.com',
        '$2y$12$vQQ0hDRlAClMP6qUDGGefuTlG0Xaz.w3AWMZrmLuAYelQKGCqM3Vm',
        '12 rue de la Paix, 75001 Paris',
        '0123456789',
        '1985-03-15',
        'active'
    ),
    (
        'Martin',
        'Jean',
        'jean.martin',
        'jean.martin@email.com',
        '$2y$12$vQQ0hDRlAClMP6qUDGGefuTlG0Xaz.w3AWMZrmLuAYelQKGCqM3Vm',
        '45 avenue des Champs, 69000 Lyon',
        '0234567890',
        '1990-07-22',
        'active'
    ),
    (
        'Leroy',
        'Sophie',
        'sophie.leroy',
        'sophie.leroy@email.com',
        '$2y$12$vQQ0hDRlAClMP6qUDGGefuTlG0Xaz.w3AWMZrmLuAYelQKGCqM3Vm',
        '23 boulevard Victor Hugo, 31000 Toulouse',
        '0345678901',
        '1988-11-08',
        'active'
    ),
    (
        'Bernard',
        'Pierre',
        'pierre.bernard',
        'pierre.bernard@email.com',
        '$2y$12$vQQ0hDRlAClMP6qUDGGefuTlG0Xaz.w3AWMZrmLuAYelQKGCqM3Vm',
        '78 rue du Commerce, 13000 Marseille',
        '0456789012',
        '1983-12-03',
        'active'
    ),
    (
        'Petit',
        'Julie',
        'julie.petit',
        'julie.petit@email.com',
        '$2y$12$vQQ0hDRlAClMP6qUDGGefuTlG0Xaz.w3AWMZrmLuAYelQKGCqM3Vm',
        '67 place de la République, 67000 Strasbourg',
        '0567890123',
        '1992-09-14',
        'active'
    ),
    (
        'Robert',
        'Michel',
        'michel.robert',
        'michel.robert@email.com',
        '$2y$12$vQQ0hDRlAClMP6qUDGGefuTlG0Xaz.w3AWMZrmLuAYelQKGCqM3Vm',
        '34 rue de Rivoli, 44000 Nantes',
        '0678901234',
        '1987-04-25',
        'active'
    ),
    (
        'Richard',
        'Emma',
        'emma.richard',
        'emma.richard@email.com',
        '$2y$12$vQQ0hDRlAClMP6qUDGGefuTlG0Xaz.w3AWMZrmLuAYelQKGCqM3Vm',
        '89 avenue de la Liberté, 59000 Lille',
        '0789012345',
        '1991-01-17',
        'active'
    ),
    (
        'Durand',
        'Thomas',
        'thomas.durand',
        'thomas.durand@email.com',
        '$2y$12$vQQ0hDRlAClMP6qUDGGefuTlG0Xaz.w3AWMZrmLuAYelQKGCqM3Vm',
        '56 cours Mirabeau, 13100 Aix-en-Provence',
        '0890123456',
        '1989-06-30',
        'active'
    ),
    (
        'Moreau',
        'Laura',
        'laura.moreau',
        'laura.moreau@email.com',
        '$2y$12$vQQ0hDRlAClMP6qUDGGefuTlG0Xaz.w3AWMZrmLuAYelQKGCqM3Vm',
        '12 quai des Belges, 76000 Rouen',
        '0901234567',
        '1986-10-12',
        'active'
    ),
    (
        'Simon',
        'Antoine',
        'antoine.simon',
        'antoine.simon@email.com',
        '$2y$12$vQQ0hDRlAClMP6qUDGGefuTlG0Xaz.w3AWMZrmLuAYelQKGCqM3Vm',
        '45 rue Saint-Antoine, 21000 Dijon',
        '0123456790',
        '1993-08-28',
        'active'
    );

-- Insertion des rôles utilisateur (attribution des types passenger/driver/both)
INSERT INTO
    `user_roles` (
        `user_id`,
        `role_id`,
        `user_role`
    )
VALUES (1, 1, 'passenger'),
    (2, 1, 'driver'),
    (3, 1, 'both'),
    (4, 1, 'driver'),
    (5, 1, 'passenger'),
    (6, 1, 'both'),
    (7, 1, 'both'),
    (8, 1, 'driver'),
    (9, 1, 'passenger'),
    (10, 1, 'both');

-- Insertion des véhicules pour les conducteurs
INSERT INTO
    `vehicles` (
        `user_id`,
        `brand_id`,
        `model`,
        `registration_number`,
        `first_registration_date`,
        `color`,
        `seats_available`,
        `energy_type`
    )
VALUES (
        2,
        1,
        'Prius',
        'AB-123-CD',
        '2020-05-15',
        'Blanc',
        4,
        'hybrid'
    ),
    (
        3,
        5,
        'Clio',
        'EF-456-GH',
        '2019-08-22',
        'Rouge',
        4,
        'essence'
    ),
    (
        4,
        7,
        'Série 3',
        'IJ-789-KL',
        '2021-03-10',
        'Noir',
        4,
        'diesel'
    ),
    (
        6,
        11,
        'Model 3',
        'MN-012-OP',
        '2022-01-18',
        'Bleu',
        4,
        'electric'
    ),
    (
        7,
        4,
        '308',
        'QR-345-ST',
        '2020-11-05',
        'Gris',
        4,
        'hybrid'
    ),
    (
        8,
        2,
        'Focus',
        'UV-678-WX',
        '2018-07-12',
        'Blanc',
        4,
        'essence'
    ),
    (
        10,
        9,
        'Golf',
        'YZ-901-AB',
        '2021-09-30',
        'Argent',
        4,
        'diesel'
    );

-- Insertion des préférences conducteur
INSERT INTO
    `drivers_preferences` (
        `user_id`,
        `smoking_allowed`,
        `animals_allowed`,
        `personalized_preferences`
    )
VALUES (
        2,
        FALSE,
        TRUE,
        'Musique calme appréciée, pas de discussions politiques'
    ),
    (
        3,
        FALSE,
        FALSE,
        'Voyage en silence ou musique douce uniquement'
    ),
    (
        4,
        TRUE,
        TRUE,
        'Ambiance décontractée, discussions bienvenues'
    ),
    (
        6,
        FALSE,
        TRUE,
        'Passionné de technologie, aime parler voitures électriques'
    ),
    (
        7,
        FALSE,
        FALSE,
        'Préfère les trajets silencieux, musique classique'
    ),
    (
        8,
        FALSE,
        TRUE,
        'Aime la bonne humeur et les discussions sur le sport'
    ),
    (
        10,
        TRUE,
        FALSE,
        'Flexible sur l\'ambiance, s\'adapte aux passagers'
    );

-- Insertion de quelques covoiturages
INSERT INTO
    `carpools` (
        `driver_id`,
        `vehicle_id`,
        `departure`,
        `arrival`,
        `departure_time`,
        `arrival_time`,
        `seats_available`,
        `price_per_seat`,
        `is_ecological`,
        `status`
    )
VALUES (
        2,
        1,
        'Lyon',
        'Paris',
        '2025-09-15 08:00:00',
        '2025-09-15 12:30:00',
        3,
        25.50,
        TRUE,
        'scheduled'
    ),
    (
        3,
        2,
        'Toulouse',
        'Bordeaux',
        '2025-09-16 14:00:00',
        '2025-09-16 16:30:00',
        2,
        18.00,
        TRUE,
        'scheduled'
    ),
    (
        4,
        3,
        'Marseille',
        'Nice',
        '2025-09-17 07:30:00',
        '2025-09-17 10:00:00',
        3,
        22.00,
        TRUE,
        'scheduled'
    ),
    (
        6,
        4,
        'Nantes',
        'Rennes',
        '2025-09-18 16:00:00',
        '2025-09-18 17:30:00',
        3,
        15.00,
        TRUE,
        'scheduled'
    );

-- === COMPTES ADMINISTRATEUR ET EMPLOYÉS ===

-- Insertion de l'administrateur (mot de passe: AdminEcoRide2025)
INSERT INTO
    `users` (
        `name`,
        `firstname`,
        `username`,
        `email`,
        `password_hash`,
        `address`,
        `phone`,
        `date_of_birth`,
        `status`
    )
VALUES (
        'Jose',
        'Jose',
        'jose.jose',
        'jose@ecoride.com',
        '$2y$12$CLI92gla.iMSyJyatTdWFehzRJbf212IMItcpCuFiTGTSWG2Ujli2',
        '1 rue de la Direction, 69000 Lyon',
        '0100000000',
        '1980-01-01',
        'active'
    );

-- Insertion des employés (mot de passe: EmployeeEco2025)
INSERT INTO
    `users` (
        `name`,
        `firstname`,
        `username`,
        `email`,
        `password_hash`,
        `address`,
        `phone`,
        `date_of_birth`,
        `status`
    )
VALUES (
        'Dubois',
        'Claire',
        'claire.dubois',
        'claire.dubois@ecoride.com',
        '$2y$12$2DTJTymwZs64MKliwIENlONE.R2KemAuD/maHuymLbN2KYhdxMyOq',
        '15 avenue du Support, 75002 Paris',
        '0200000001',
        '1985-05-15',
        'active'
    ),
    (
        'Reymond',
        'Alexandre',
        'alexandre.reymond',
        'alexandre.reymond@ecoride.com',
        '$2y$12$2DTJTymwZs64MKliwIENlONE.R2KemAuD/maHuymLbN2KYhdxMyOq',
        '42 rue de la Modération, 75003 Paris',
        '0200000002',
        '1988-09-20',
        'active'
    );

-- Attribution des rôles
INSERT INTO
    `user_roles` (`user_id`, `role_id`)
VALUES (11, 2),
    (12, 3),
    (13, 3);