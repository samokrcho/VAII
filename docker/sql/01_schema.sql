-- Dračí Doupě II – schéma databázy pre MariaDB
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS dennik_zvlastna_schopnost;
DROP TABLE IF EXISTS dennik_povolanie;
DROP TABLE IF EXISTS dennik;
DROP TABLE IF EXISTS zvlastna_schopnost;
DROP TABLE IF EXISTS rasova_schopnost;
DROP TABLE IF EXISTS povolanie;
DROP TABLE IF EXISTS rasa;
DROP TABLE IF EXISTS pouzivatel;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE pouzivatel (
 id_pouzivatel INT UNSIGNED NOT NULL AUTO_INCREMENT,
 meno VARCHAR(100) NOT NULL, email VARCHAR(190) NOT NULL,
 heslo_hash VARCHAR(255) NOT NULL,
 rola ENUM('user','gm','admin') NOT NULL DEFAULT 'user',
 vytvoreny DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (id_pouzivatel), UNIQUE KEY uq_pouzivatel_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rasa (
 id_rasa INT UNSIGNED NOT NULL AUTO_INCREMENT,
 nazov VARCHAR(100) NOT NULL, popis TEXT NULL,
 PRIMARY KEY (id_rasa), UNIQUE KEY uq_rasa_nazov (nazov)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Jedna rasa môže ponúkať viac rasových schopností; denník si vyberie jednu.
CREATE TABLE rasova_schopnost (
 id_rasova_schopnost INT UNSIGNED NOT NULL AUTO_INCREMENT,
 id_rasa INT UNSIGNED NOT NULL, nazov VARCHAR(150) NOT NULL,
 vlastnost ENUM('telo','duse','vliv') NOT NULL,
 citat TEXT NULL, text TEXT NULL,
 PRIMARY KEY (id_rasova_schopnost),
 UNIQUE KEY uq_rasova_schopnost_rasa_id (id_rasa,id_rasova_schopnost),
 UNIQUE KEY uq_rasova_schopnost_nazov (id_rasa,nazov),
 CONSTRAINT fk_rasova_schopnost_rasa FOREIGN KEY (id_rasa) REFERENCES rasa(id_rasa)
 ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE povolanie (
 id_povolanie INT UNSIGNED NOT NULL AUTO_INCREMENT,
 nazov VARCHAR(100) NOT NULL, popis TEXT NULL,
 PRIMARY KEY (id_povolanie), UNIQUE KEY uq_povolanie_nazov (nazov)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE zvlastna_schopnost (
 id_schopnost INT UNSIGNED NOT NULL AUTO_INCREMENT,
 id_povolanie INT UNSIGNED NOT NULL, nazov VARCHAR(150) NOT NULL,
 vlastnost ENUM('telo','duse','vliv') NOT NULL,
 citat TEXT NULL, text TEXT NULL,
 minimalna_uroven TINYINT UNSIGNED NOT NULL DEFAULT 0,
 PRIMARY KEY (id_schopnost),
 UNIQUE KEY uq_zs_povolanie_id (id_povolanie,id_schopnost),
 UNIQUE KEY uq_zs_povolanie_nazov (id_povolanie,nazov),
 CONSTRAINT chk_zs_min_uroven CHECK (minimalna_uroven BETWEEN 0 AND 5),
 CONSTRAINT fk_zs_povolanie FOREIGN KEY (id_povolanie) REFERENCES povolanie(id_povolanie)
 ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE dennik (
 id_dennik INT UNSIGNED NOT NULL AUTO_INCREMENT,
 id_pouzivatel INT UNSIGNED NOT NULL, id_rasa INT UNSIGNED NOT NULL,
 id_rasova_schopnost INT UNSIGNED NOT NULL,
 meno_postavy VARCHAR(120) NOT NULL, popis TEXT NULL,
 je_verejny TINYINT(1) NOT NULL DEFAULT 0,
 telo TINYINT NOT NULL DEFAULT 0, dusa TINYINT NOT NULL DEFAULT 0,
 vplyv TINYINT NOT NULL DEFAULT 0,
 ohrozenie TINYINT UNSIGNED NOT NULL DEFAULT 0,
 vyhoda TINYINT UNSIGNED NOT NULL DEFAULT 0,
 dukaty INT UNSIGNED NOT NULL DEFAULT 0, grose INT UNSIGNED NOT NULL DEFAULT 0,
 haliere INT UNSIGNED NOT NULL DEFAULT 0, vybavenie TEXT NULL,
 vytvoreny DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 upraveny DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (id_dennik), KEY ix_dennik_pouzivatel (id_pouzivatel),
 CONSTRAINT fk_dennik_pouzivatel FOREIGN KEY (id_pouzivatel) REFERENCES pouzivatel(id_pouzivatel)
 ON UPDATE CASCADE ON DELETE CASCADE,
 CONSTRAINT fk_dennik_rasova_schopnost FOREIGN KEY (id_rasa,id_rasova_schopnost)
 REFERENCES rasova_schopnost(id_rasa,id_rasova_schopnost)
 ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE dennik_povolanie (
 id_dennik_povolanie INT UNSIGNED NOT NULL AUTO_INCREMENT,
 id_dennik INT UNSIGNED NOT NULL, id_povolanie INT UNSIGNED NOT NULL,
 uroven TINYINT UNSIGNED NOT NULL DEFAULT 0,
 PRIMARY KEY (id_dennik_povolanie),
 UNIQUE KEY uq_dennik_povolanie (id_dennik,id_povolanie),
 CONSTRAINT chk_dp_uroven CHECK (uroven BETWEEN 0 AND 5),
 CONSTRAINT fk_dp_dennik FOREIGN KEY (id_dennik) REFERENCES dennik(id_dennik)
 ON UPDATE CASCADE ON DELETE CASCADE,
 CONSTRAINT fk_dp_povolanie FOREIGN KEY (id_povolanie) REFERENCES povolanie(id_povolanie)
 ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE dennik_zvlastna_schopnost (
 id_dennik_zvlastna_schopnost INT UNSIGNED NOT NULL AUTO_INCREMENT,
 id_dennik INT UNSIGNED NOT NULL, id_povolanie INT UNSIGNED NOT NULL,
 id_schopnost INT UNSIGNED NOT NULL, uroven_ziskania TINYINT UNSIGNED NOT NULL,
 PRIMARY KEY (id_dennik_zvlastna_schopnost),
 UNIQUE KEY uq_dzs_dennik_schopnost (id_dennik,id_povolanie,id_schopnost),
 UNIQUE KEY uq_dzs_dennik_uroven (id_dennik,id_povolanie,uroven_ziskania),
 CONSTRAINT chk_dzs_uroven CHECK (uroven_ziskania BETWEEN 1 AND 5),
 CONSTRAINT fk_dzs_dennik_povolanie FOREIGN KEY (id_dennik,id_povolanie)
 REFERENCES dennik_povolanie(id_dennik,id_povolanie)
 ON UPDATE CASCADE ON DELETE CASCADE,
 CONSTRAINT fk_dzs_povolanie_schopnost FOREIGN KEY (id_povolanie,id_schopnost)
 REFERENCES zvlastna_schopnost(id_povolanie,id_schopnost)
 ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kontrola dostupnosti schopnosti pri jej výbere.
DELIMITER //
CREATE TRIGGER bi_dzs_check_level
BEFORE INSERT ON dennik_zvlastna_schopnost
FOR EACH ROW
BEGIN
    DECLARE v_uroven_povolania TINYINT UNSIGNED;
    DECLARE v_min_uroven TINYINT UNSIGNED;
    SELECT dp.uroven, zs.minimalna_uroven
      INTO v_uroven_povolania, v_min_uroven
      FROM dennik_povolanie dp
      INNER JOIN zvlastna_schopnost zs
        ON zs.id_povolanie = dp.id_povolanie
       AND zs.id_schopnost = NEW.id_schopnost
     WHERE dp.id_dennik = NEW.id_dennik
       AND dp.id_povolanie = NEW.id_povolanie;
    IF NEW.uroven_ziskania > v_uroven_povolania THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Schopnosť nemožno získať na vyššej úrovni, než má povolanie.';
    END IF;
    IF NEW.uroven_ziskania < v_min_uroven THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Schopnosť ešte nie je dostupná na tejto úrovni.';
    END IF;
END//

CREATE TRIGGER bu_dzs_check_level
BEFORE UPDATE ON dennik_zvlastna_schopnost
FOR EACH ROW
BEGIN
    DECLARE v_uroven_povolania TINYINT UNSIGNED;
    DECLARE v_min_uroven TINYINT UNSIGNED;
    SELECT dp.uroven, zs.minimalna_uroven
      INTO v_uroven_povolania, v_min_uroven
      FROM dennik_povolanie dp
      INNER JOIN zvlastna_schopnost zs
        ON zs.id_povolanie = dp.id_povolanie
       AND zs.id_schopnost = NEW.id_schopnost
     WHERE dp.id_dennik = NEW.id_dennik
       AND dp.id_povolanie = NEW.id_povolanie;
    IF NEW.uroven_ziskania > v_uroven_povolania THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Schopnosť nemožno získať na vyššej úrovni, než má povolanie.';
    END IF;
    IF NEW.uroven_ziskania < v_min_uroven THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Schopnosť ešte nie je dostupná na tejto úrovni.';
    END IF;
END//

CREATE TRIGGER bu_dp_prevent_invalid_level
BEFORE UPDATE ON dennik_povolanie
FOR EACH ROW
BEGIN
    DECLARE v_max_ziskania TINYINT UNSIGNED;
    SELECT COALESCE(MAX(uroven_ziskania), 0)
      INTO v_max_ziskania
      FROM dennik_zvlastna_schopnost
     WHERE id_dennik = OLD.id_dennik
       AND id_povolanie = OLD.id_povolanie;
    IF NEW.uroven < v_max_ziskania THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Úroveň povolania nemožno znížiť pod úroveň už získanej schopnosti.';
    END IF;
END//
DELIMITER ;
