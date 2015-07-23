-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:32:46 2005
--
-- Generating CREATE statement for:
-- TABLE FAMILY:pamdemo.parameter_descriptions

CREATE TABLE pamdemo.parameter_descriptions (
    `id` INT ( 4 ) NOT NULL AUTO_INCREMENT,
    `Application_class` VARCHAR ( 50 ) NOT NULL DEFAULT '',
    `parameter` VARCHAR ( 50 ) NOT NULL DEFAULT '',
    `param_type` CHAR ( 3 ) NOT NULL DEFAULT 'CON',
    `description` text,
    `collector` VARCHAR ( 50 ) DEFAULT NULL,
    `app_class_alias` VARCHAR ( 50 ) DEFAULT NULL,
	`parameter_alias` VARCHAR ( 50 ) DEFAULT NULL,
	PRIMARY KEY ( `id` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

