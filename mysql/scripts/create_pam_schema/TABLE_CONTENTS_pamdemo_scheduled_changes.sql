-- This DDL was reverse engineered by
-- Toad for MySQL, Version 1.0.3
--
-- at:   JLT5160
-- from: localhost:patrol_report, an MySQL 4.1.8-nt-log database
--
-- on:   Mon Apr 11 11:35:05 2005
--
-- Generating CREATE statement for:
-- TABLE CONTENTS:pamdemo.scheduled_changes

INSERT INTO pamdemo.scheduled_changes (ID,RequestID,SchedulerID,Retries,Status,Date_Entered,Date_Modified,Start_Time,End_Time,Comment)
 VALUES ('1','2','2','10',NULL,'2005-03-21 21:18:28','2005-03-21 21:18:28','2005-03-22 21:17:57','2005-03-23 21:18:01','ORACLE DEPLOYMENT'),
	('2','5','2','5',NULL,'2005-03-21 21:43:05','2005-03-21 21:43:05','2005-03-22 21:42:42','2005-03-22 21:42:46','schedule'),
	('3','4','2','10',NULL,'2005-03-21 21:50:42','2005-03-21 21:50:42','2005-03-22 21:50:15','2005-03-22 21:50:18','Only these hosts'),
	('4','7','2','10',NULL,'2005-03-21 22:09:11','2005-03-21 22:09:11','2005-03-22 22:08:31','2005-03-22 22:08:35','Scheduled only for half'),
	('5','11','2','10',NULL,'2005-03-21 22:13:59','2005-03-21 22:13:59','2005-03-22 22:13:43','2005-03-22 22:13:46','Scheduled'),
	('6','9','2','10',NULL,'2005-03-21 22:17:18','2005-03-21 22:17:18','2005-03-22 22:16:42','2005-03-23 22:16:46','Scheduled'),
	('7','13','2','10',NULL,'2005-03-22 10:18:02','2005-03-22 10:18:02','2005-03-23 11:17:32','2005-03-24 11:17:36','Schedule only 3 hosts today'),
	('8','14','2','10',NULL,'2005-03-22 10:24:39','2005-03-22 10:24:39','2005-03-23 11:24:09','2005-03-24 11:24:12','Scheduled only for 4'),
	('9','18','2','10',NULL,'2005-03-22 14:00:53','2005-03-22 14:00:53','2005-03-23 13:59:57','2005-03-23 14:00:03','Only Half of teh servers');
COMMIT;

