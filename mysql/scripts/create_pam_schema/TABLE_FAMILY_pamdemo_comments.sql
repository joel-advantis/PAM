-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:32:46 2005
--
-- Generating CREATE statement for:
-- TABLE FAMILY:pamdemo.comments

CREATE TABLE pamdemo.comments (
    `ID` INT ( 5 ) NOT NULL AUTO_INCREMENT,
    `UserID` INT ( 4 ) DEFAULT NULL,
    `Date_Entered` datetime DEFAULT NULL,
    `Table_Name` VARCHAR ( 30 ) DEFAULT NULL,
    `RecordID` INT ( 4 ) DEFAULT NULL,
    `Comment` text,
    PRIMARY KEY ( `ID` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

