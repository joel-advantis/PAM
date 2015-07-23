-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:32:45 2005
--
-- Generating CREATE statement for:
-- TABLE FAMILY:pamdemo.actions

CREATE TABLE pamdemo.actions (
    `id` INT ( 4 ) NOT NULL AUTO_INCREMENT,
    `action` VARCHAR ( 50 ) DEFAULT NULL,
    PRIMARY KEY ( `id` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

