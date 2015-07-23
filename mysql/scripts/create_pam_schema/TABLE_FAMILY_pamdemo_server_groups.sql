-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:32:46 2005
--
-- Generating CREATE statement for:
-- TABLE FAMILY:pamdemo.server_groups

CREATE TABLE pamdemo.server_groups (
    `id` INT ( 4 ) NOT NULL AUTO_INCREMENT,
    `pid` INT ( 4 ) DEFAULT NULL,
    `server_group` text NOT NULL,
    PRIMARY KEY ( `id` ),
    KEY `si1` ( `server_group` ( 90 ),
		`id` ),
    KEY `si2` ( `server_group` ( 91 ),
		`id` ),
    KEY `si3` ( `id`,
		`server_group` ( 91 ) )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

