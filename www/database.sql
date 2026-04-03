

CREATE DATABASE IF NOT EXISTS monsite CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE monsite;

CREATE TABLE IF NOT EXISTS user (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(255) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('admin', 'pilote', 'etudiant') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS profile (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NOT NULL UNIQUE,
    first_name      VARCHAR(100) NOT NULL,
    last_name       VARCHAR(100) NOT NULL,
    is_active       BOOLEAN DEFAULT TRUE,
    stage_status    ENUM('searching', 'found', 'not_searching') DEFAULT 'searching',
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS location (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    city        VARCHAR(100) NOT NULL,
    department  VARCHAR(100),
    postal_code VARCHAR(10),
    region      VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS company (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    description TEXT,
    email       VARCHAR(255),
    phone       VARCHAR(20),
    rating      DECIMAL(2,1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS company_rating (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    user_id    INT NOT NULL,
    rating     DECIMAL(2,1) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_company (company_id, user_id),
    FOREIGN KEY (company_id) REFERENCES company(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES user(id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS offer (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    title            VARCHAR(255) NOT NULL,
    description      TEXT,
    salary           VARCHAR(100),
    type             ENUM('internship', 'apprenticeship') NOT NULL,
    mode             ENUM('on_site', 'remote', 'hybrid') NOT NULL,
    publication_date DATE DEFAULT (CURRENT_DATE),
    is_active        BOOLEAN DEFAULT TRUE,
    company_id       INT NOT NULL,
    location_id      INT NOT NULL,
    FOREIGN KEY (company_id)  REFERENCES company(id)  ON DELETE CASCADE,
    FOREIGN KEY (location_id) REFERENCES location(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS skill (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS offer_skill (
    offer_id INT NOT NULL,
    skill_id INT NOT NULL,
    PRIMARY KEY (offer_id, skill_id),
    FOREIGN KEY (offer_id) REFERENCES offer(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skill(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS application (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    student_id       INT NOT NULL,
    offer_id         INT NOT NULL,
    status           ENUM('sent', 'accepted', 'refused') DEFAULT 'sent',
    message          TEXT,
    cv_path          VARCHAR(255) DEFAULT '',
    lm_path          VARCHAR(255) DEFAULT '',
    application_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (offer_id)   REFERENCES offer(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS spontaneous_application (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    student_id       INT NOT NULL,
    target_company   VARCHAR(255) DEFAULT '',
    target_position  VARCHAR(255) DEFAULT '',
    contract_type    VARCHAR(50) DEFAULT '',
    message          TEXT,
    cv_path          VARCHAR(255) DEFAULT '',
    lm_path          VARCHAR(255) DEFAULT '',
    application_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES user(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS wishlist (
    student_id INT NOT NULL,
    offer_id   INT NOT NULL,
    added_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (student_id, offer_id),
    FOREIGN KEY (student_id) REFERENCES user(id)  ON DELETE CASCADE,
    FOREIGN KEY (offer_id)   REFERENCES offer(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS promotion (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    name     VARCHAR(255) NOT NULL,
    year     YEAR NOT NULL,
    pilot_id INT NOT NULL,
    FOREIGN KEY (pilot_id) REFERENCES user(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS student_promotion (
    student_id   INT NOT NULL,
    promotion_id INT NOT NULL,
    PRIMARY KEY (student_id, promotion_id),
    FOREIGN KEY (student_id)   REFERENCES user(id)       ON DELETE CASCADE,
    FOREIGN KEY (promotion_id) REFERENCES promotion(id)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
