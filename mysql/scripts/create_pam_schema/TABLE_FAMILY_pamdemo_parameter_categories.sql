-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:32:46 2005
--
-- Generating CREATE statement for:
-- TABLE FAMILY:pamdemo.parameter_categories

CREATE TABLE pamdemo.parameter_categories (
    `id` INT ( 4 ) NOT NULL AUTO_INCREMENT,
    `categoryid` INT ( 4 ) DEFAULT NULL,
    `parameterid` INT ( 4 ) DEFAULT NULL,
    PRIMARY KEY ( `id` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

