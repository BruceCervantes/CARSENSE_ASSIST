-- ══════════════════════════════════════════════
-- CARSENSE — Esquema de base de datos
-- Motor: MySQL 8.x / MariaDB 10.x (XAMPP)
-- Ejecutar en phpMyAdmin o CLI: mysql -u root < database.sql
-- ══════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS carsense
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE carsense;

-- ── Usuarios ─────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(255)        NOT NULL,
  email        VARCHAR(255) UNIQUE NOT NULL,
  password_hash VARCHAR(255)       NOT NULL,
  initials     VARCHAR(5)          NOT NULL DEFAULT '',
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── Vehículos ─────────────────────────────────
CREATE TABLE IF NOT EXISTS vehicles (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT          NOT NULL,
  brand        VARCHAR(100) NOT NULL,
  model        VARCHAR(100) NOT NULL,
  year         VARCHAR(4)   NOT NULL DEFAULT '',
  km           VARCHAR(20)  NOT NULL DEFAULT '0',
  plate        VARCHAR(20)  NOT NULL DEFAULT '-' UNIQUE,
  color        VARCHAR(50)  NOT NULL DEFAULT 'Sin especificar',
  accent_color VARCHAR(20)  NOT NULL DEFAULT '#e03030',
  is_active    TINYINT(1)   NOT NULL DEFAULT 0,
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Sistemas ──────────────────────────────────
CREATE TABLE IF NOT EXISTS systems (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  slug            VARCHAR(100) UNIQUE NOT NULL,
  name            VARCHAR(255)        NOT NULL,
  description     TEXT,
  color           VARCHAR(20)         NOT NULL DEFAULT '#888',
  criticality     VARCHAR(50)         NOT NULL DEFAULT 'Media',
  image_url       TEXT,
  component_count INT                 NOT NULL DEFAULT 0,
  symptom_count   INT                 NOT NULL DEFAULT 0,
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── Hotpoints del diagrama de sistema ─────────
CREATE TABLE IF NOT EXISTS system_hotpoints (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  system_slug    VARCHAR(100) NOT NULL,
  component_slug VARCHAR(100) NOT NULL,
  name           VARCHAR(255) NOT NULL,
  x_pos          DECIMAL(5,2) NOT NULL DEFAULT 0,
  y_pos          DECIMAL(5,2) NOT NULL DEFAULT 0,
  sort_order     INT          NOT NULL DEFAULT 0,
  FOREIGN KEY (system_slug) REFERENCES systems(slug) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Síntomas del sistema ──────────────────────
CREATE TABLE IF NOT EXISTS system_symptoms (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  system_slug VARCHAR(100) NOT NULL,
  label       VARCHAR(255) NOT NULL,
  result_slug VARCHAR(100) NOT NULL DEFAULT '',
  sort_order  INT          NOT NULL DEFAULT 0,
  FOREIGN KEY (system_slug) REFERENCES systems(slug) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Mantenimiento del sistema ─────────────────
CREATE TABLE IF NOT EXISTS system_maintenance (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  system_slug   VARCHAR(100) NOT NULL,
  label         VARCHAR(255) NOT NULL,
  interval_text TEXT         NOT NULL,
  sort_order    INT          NOT NULL DEFAULT 0,
  FOREIGN KEY (system_slug) REFERENCES systems(slug) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Componentes ───────────────────────────────
CREATE TABLE IF NOT EXISTS components (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  slug        VARCHAR(100) UNIQUE NOT NULL,
  system_slug VARCHAR(100)        NOT NULL,
  name        VARCHAR(255)        NOT NULL,
  description TEXT,
  wear        INT                 NOT NULL DEFAULT 0,
  wear_status ENUM('ok','warning','critical') NOT NULL DEFAULT 'ok',
  wear_label  TEXT,
  image_url   TEXT,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (system_slug) REFERENCES systems(slug) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Especificaciones de componente ────────────
CREATE TABLE IF NOT EXISTS component_specs (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  component_slug VARCHAR(100) NOT NULL,
  spec_label     VARCHAR(255) NOT NULL,
  spec_value     VARCHAR(255) NOT NULL,
  sort_order     INT          NOT NULL DEFAULT 0,
  FOREIGN KEY (component_slug) REFERENCES components(slug) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Síntomas de componente ────────────────────
CREATE TABLE IF NOT EXISTS component_symptoms (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  component_slug VARCHAR(100) NOT NULL,
  symptom        TEXT         NOT NULL,
  sort_order     INT          NOT NULL DEFAULT 0,
  FOREIGN KEY (component_slug) REFERENCES components(slug) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Consejos de componente ────────────────────
CREATE TABLE IF NOT EXISTS component_tips (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  component_slug VARCHAR(100) NOT NULL,
  tip            TEXT         NOT NULL,
  sort_order     INT          NOT NULL DEFAULT 0,
  FOREIGN KEY (component_slug) REFERENCES components(slug) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Resultados de diagnóstico ─────────────────
CREATE TABLE IF NOT EXISTS diagnostic_results (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  numeric_id      VARCHAR(10)         NOT NULL,
  slug            VARCHAR(100) UNIQUE NOT NULL,
  title           VARCHAR(255)        NOT NULL,
  description     TEXT,
  priority        ENUM('Alta','Media','Baja') NOT NULL DEFAULT 'Media',
  system_slug     VARCHAR(100),
  price_min_mxn   INT NULL,
  price_max_mxn   INT NULL,
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (system_slug) REFERENCES systems(slug) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── Tags del diagnóstico ──────────────────────
CREATE TABLE IF NOT EXISTS diagnostic_tags (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  result_slug VARCHAR(100) NOT NULL,
  tag         VARCHAR(100) NOT NULL,
  sort_order  INT          NOT NULL DEFAULT 0,
  FOREIGN KEY (result_slug) REFERENCES diagnostic_results(slug) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Zonas del diagnóstico ─────────────────────
CREATE TABLE IF NOT EXISTS diagnostic_zones (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  result_slug VARCHAR(100) NOT NULL,
  zone        VARCHAR(100) NOT NULL,
  FOREIGN KEY (result_slug) REFERENCES diagnostic_results(slug) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Cuándo ocurre el diagnóstico ─────────────
CREATE TABLE IF NOT EXISTS diagnostic_when (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  result_slug VARCHAR(100) NOT NULL,
  when_text   VARCHAR(100) NOT NULL,
  FOREIGN KEY (result_slug) REFERENCES diagnostic_results(slug) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Sensaciones del diagnóstico ───────────────
CREATE TABLE IF NOT EXISTS diagnostic_sensations (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  result_slug VARCHAR(100) NOT NULL,
  sensation   VARCHAR(100) NOT NULL,
  FOREIGN KEY (result_slug) REFERENCES diagnostic_results(slug) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Notificaciones ────────────────────────────
CREATE TABLE IF NOT EXISTS notifications (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT,
  title       VARCHAR(255) NOT NULL,
  description TEXT,
  type        ENUM('warning','success','info') NOT NULL DEFAULT 'info',
  time_label  VARCHAR(100) NOT NULL DEFAULT '',
  is_read     TINYINT(1)   NOT NULL DEFAULT 0,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Historial de consultas ────────────────────
CREATE TABLE IF NOT EXISTS consultation_history (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT,
  result_slug VARCHAR(100) NOT NULL DEFAULT '',
  title       VARCHAR(255) NOT NULL DEFAULT '',
  system_name VARCHAR(100) NOT NULL DEFAULT '',
  severity    ENUM('alta','media','baja') NOT NULL DEFAULT 'media',
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Recordatorios de mantenimiento ───────────
CREATE TABLE IF NOT EXISTS reminders (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  user_id   INT,
  label     VARCHAR(255)                    NOT NULL,
  due_text  VARCHAR(100)                    NOT NULL DEFAULT '',
  urgency   ENUM('alta','media','baja')     NOT NULL DEFAULT 'baja',
  is_done   TINYINT(1)                      NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
