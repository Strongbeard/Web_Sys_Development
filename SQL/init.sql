/* DATABASE USER ACCESS */
GRANT ALL ON TA_Hunter.* TO 'TA_Hunter'@'localhost' IDENTIFIED BY 'web_sys_dev_user';

/* DATABASE CREATION */
DROP DATABASE IF EXISTS TA_Hunter;
CREATE DATABASE TA_Hunter;
USE TA_Hunter;

/* TABLE CREATION */
DROP TABLE IF EXISTS users;
CREATE TABLE users(
	email VARCHAR(255) NOT NULL,
	firstName VARCHAR(255),
	lastName VARCHAR(255),
	isAdmin BOOL NOT NULL,
	isTA BOOL NOT NULL,
	isTutor BOOL NOT NULL,
	isStudent BOOL NOT NULL,
	PRIMARY KEY(email)
);

DROP TABLE IF EXISTS courses;
CREATE TABLE courses(
	subj CHAR(4) NOT NULL,
	crse MEDIUMINT(3) UNSIGNED NOT NULL,
	name VARCHAR(255) NOT NULL,
	ta_code VARCHAR(50) NOT NULL UNIQUE,
	PRIMARY KEY(subj, crse)
);

DROP TABLE IF EXISTS Passwords;
CREATE TABLE Passwords(
	email VARCHAR(255) NOT NULL,
	password VARCHAR(255),
	PRIMARY KEY(email),
	FOREIGN KEY(email) REFERENCES Users(email)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

DROP TABLE IF EXISTS students_courses;
CREATE TABLE students_courses(
	email VARCHAR(255) NOT NULL,
	subj CHAR(4) NOT NULL,
	crse MEDIUMINT(3) UNSIGNED NOT NULL,
	PRIMARY KEY(email, subj, crse),
	FOREIGN KEY(email) REFERENCES users(email)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	FOREIGN KEY(subj, crse) REFERENCES courses(subj, crse)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

DROP TABLE IF EXISTS TAs_courses;
CREATE TABLE TAs_courses(
	email VARCHAR(255) NOT NULL,
	subj CHAR(4) NOT NULL,
	crse MEDIUMINT(3) UNSIGNED NOT NULL,
	PRIMARY KEY(email, subj, crse),
	FOREIGN KEY(email) REFERENCES users(email)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	FOREIGN KEY(subj, crse) REFERENCES courses(subj, crse)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

DROP TABLE IF EXISTS TA_Hours;
CREATE TABLE TA_Hours(
	email VARCHAR(255) NOT NULL,
	subj CHAR(4) NOT NULL,
	crse MEDIUMINT(3) UNSIGNED NOT NULL,
	week_day ENUM('SUNDAY','MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY') NOT NULL,
	start_time TIME NOT NULL,
	end_time TIME NOT NULL,
	PRIMARY KEY(email, subj, crse, week_day),
	FOREIGN KEY(email, subj, crse) REFERENCES tas_courses(email, subj, crse)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);