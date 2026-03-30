-- =============================================
-- CesiTonJob - Database Schema
-- =============================================

CREATE DATABASE IF NOT EXISTS monsite CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE monsite;

-- =============================================
-- TABLE: user
-- Stores authentication data for all users
-- =============================================
CREATE TABLE user (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(255) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('admin', 'pilote', 'etudiant') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- TABLE: profile
-- Stores personal information for each user
-- is_active allows disabling an account without deleting it
-- =============================================
CREATE TABLE profile (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name  VARCHAR(100) NOT NULL,
    is_active  BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);

-- =============================================
-- TABLE: location
-- Stores locations to avoid redundancy in offers
-- =============================================
CREATE TABLE location (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    city        VARCHAR(100) NOT NULL,
    department  VARCHAR(100),
    postal_code VARCHAR(10),
    region      VARCHAR(100)
);

-- =============================================
-- TABLE: company
-- Stores company information
-- =============================================
CREATE TABLE company (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    description TEXT,
    email       VARCHAR(255),
    phone       VARCHAR(20),
    rating      DECIMAL(2,1) DEFAULT 0
);

-- =============================================
-- TABLE: offer
-- Stores internship/job offers
-- =============================================
CREATE TABLE offer (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    title            VARCHAR(255) NOT NULL,
    description      TEXT,
    salary           VARCHAR(100),
    type             ENUM('internship', 'apprenticeship', 'cdi', 'cdd') NOT NULL,
    mode             ENUM('on_site', 'remote', 'hybrid') NOT NULL,
    publication_date DATE DEFAULT (CURRENT_DATE),
    is_active        BOOLEAN DEFAULT TRUE,
    company_id       INT NOT NULL,
    location_id      INT NOT NULL,
    FOREIGN KEY (company_id)  REFERENCES company(id)  ON DELETE CASCADE,
    FOREIGN KEY (location_id) REFERENCES location(id) ON DELETE RESTRICT
);

-- =============================================
-- TABLE: skill
-- Stores skills to avoid redundancy
-- =============================================
CREATE TABLE skill (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- =============================================
-- TABLE: offer_skill
-- Links offers to skills (many-to-many)
-- =============================================
CREATE TABLE offer_skill (
    offer_id INT NOT NULL,
    skill_id INT NOT NULL,
    PRIMARY KEY (offer_id, skill_id),
    FOREIGN KEY (offer_id) REFERENCES offer(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skill(id) ON DELETE CASCADE
);

-- =============================================
-- TABLE: application
-- Stores student applications to offers
-- =============================================
CREATE TABLE application (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    student_id       INT NOT NULL,
    offer_id         INT NOT NULL,
    status           ENUM('sent', 'accepted', 'refused') DEFAULT 'sent',
    message          TEXT,
    application_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (offer_id)   REFERENCES offer(id) ON DELETE CASCADE
);

-- =============================================
-- TABLE: wishlist
-- Stores offers saved by students
-- =============================================
CREATE TABLE wishlist (
    student_id INT NOT NULL,
    offer_id   INT NOT NULL,
    added_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (student_id, offer_id),
    FOREIGN KEY (student_id) REFERENCES user(id)  ON DELETE CASCADE,
    FOREIGN KEY (offer_id)   REFERENCES offer(id) ON DELETE CASCADE
);

-- =============================================
-- TABLE: promotion
-- Stores student groups managed by pilots
-- =============================================
CREATE TABLE promotion (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    name     VARCHAR(255) NOT NULL,
    year     YEAR NOT NULL,
    pilot_id INT NOT NULL,
    FOREIGN KEY (pilot_id) REFERENCES user(id) ON DELETE RESTRICT
);

-- =============================================
-- TABLE: student_promotion
-- Links students to promotions (many-to-many)
-- =============================================
CREATE TABLE student_promotion (
    student_id   INT NOT NULL,
    promotion_id INT NOT NULL,
    PRIMARY KEY (student_id, promotion_id),
    FOREIGN KEY (student_id)   REFERENCES user(id)       ON DELETE CASCADE,
    FOREIGN KEY (promotion_id) REFERENCES promotion(id)  ON DELETE CASCADE
);
