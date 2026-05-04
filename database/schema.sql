CREATE DATABASE IF NOT EXISTS bdms
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE bdms;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS blood_requests;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS otps;
DROP TABLE IF EXISTS donor_profiles;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  full_name     VARCHAR(120)    NOT NULL,
  email         VARCHAR(120)    NOT NULL,
  phone         VARCHAR(20)     NOT NULL,
  password_hash VARCHAR(255)    NOT NULL,
  role          ENUM('ADMIN','DONOR') NOT NULL DEFAULT 'DONOR',
  is_verified   TINYINT(1)      NOT NULL DEFAULT 0,
  is_active     TINYINT(1)      NOT NULL DEFAULT 1,
  created_at    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email),
  KEY idx_users_role (role)
) ENGINE=InnoDB;

CREATE TABLE donor_profiles (
  user_id            BIGINT UNSIGNED NOT NULL,
  age                TINYINT UNSIGNED NOT NULL,
  blood_group        VARCHAR(3)      NOT NULL,
  pincode            VARCHAR(10)     NOT NULL,
  last_donation_date DATE            NULL,
  created_at         TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at         TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id),
  KEY idx_donor_blood_group (blood_group),
  KEY idx_donor_pincode (pincode),
  CONSTRAINT fk_donor_user FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE otps (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id     BIGINT UNSIGNED NOT NULL,
  code        CHAR(6)         NOT NULL,
  expires_at  DATETIME        NOT NULL,
  consumed_at DATETIME        NULL,
  created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_otp_user (user_id),
  CONSTRAINT fk_otp_user FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE events (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  title       VARCHAR(200)    NOT NULL,
  message     TEXT            NOT NULL,
  created_by  BIGINT UNSIGNED NULL,
  created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_events_created_at (created_at),
  CONSTRAINT fk_events_user FOREIGN KEY (created_by) REFERENCES users(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE blood_requests (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  requester_name  VARCHAR(120)    NOT NULL,
  requester_phone VARCHAR(20)     NOT NULL,
  blood_group     VARCHAR(3)      NOT NULL,
  pincode         VARCHAR(10)     NULL,
  notes           TEXT            NULL,
  status          ENUM('OPEN','CLOSED') NOT NULL DEFAULT 'OPEN',
  created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_requests_blood_group (blood_group),
  KEY idx_requests_status (status)
) ENGINE=InnoDB;
