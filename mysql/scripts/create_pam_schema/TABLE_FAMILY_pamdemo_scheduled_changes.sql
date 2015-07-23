-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:32:46 2005
--
-- Generating CREATE statement for:
-- TABLE FAMILY:pamdemo.scheduled_changes

CREATE TABLE pamdemo.scheduled_changes (
    `ID` INT ( 4 ) NOT NULL AUTO_INCREMENT,
    `RequestID` INT ( 4 ) DEFAULT NULL,
    `SchedulerID` INT ( 4 ) DEFAULT NULL,
    `Retries` INT ( 2 ) DEFAULT NULL,
    `Status` INT ( 1 ) DEFAULT NULL,
    `Date_Entered` datetime DEFAULT NULL,
    `Date_Modified` datetime DEFAULT NULL,
    `Start_Time` datetime DEFAULT NULL,
    `End_Time` datetime DEFAULT NULL,
    `Comment` text,
    PRIMARY KEY ( `ID` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

