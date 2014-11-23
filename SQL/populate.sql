USE TA_Hunter;

/* USERS */
INSERT IGNORE INTO users (userid, email, isAdmin, isStudent, isTA, isTutor, firstName, lastName) VALUES (1,'mahonk3@rpi.edu',FALSE,TRUE,FALSE,FALSE,'Kevin','Mahon'); /* password = mahonk3 */
INSERT IGNORE INTO users (userid, email, isAdmin, isStudent, isTA, isTutor, firstName, lastName) VALUES (2,'ta@test.com',FALSE,FALSE,TRUE,FALSE,'TA_First','TA_Last');

/* PASSWORDS */
INSERT IGNORE INTO passwords(userid, password) VALUES (1,'$2y$10$KN0.2dx2J7ZCj1aPqhruwuZ7YR0soWcv/rWX7FuURB2U9EC5wUe2u');
INSERT IGNORE INTO passwords(userid, password) VALUES (2,'$2y$10$767S3Y8D7uui6wtKtrXJge7IgE8Wks0qq2MrpgRwGh8bkUTGfandy');

/* COURSES */
INSERT IGNORE INTO courses(name, subj, crse) VALUES ( 'BASIC DRAWING', 'ARTS', 1200 );
INSERT IGNORE INTO courses(name, subj, crse) VALUES ( 'ART HISTORY I:FROM PALEOLITHIC TO RENAISSANCE', 'ARTS', 2530 );
INSERT IGNORE INTO courses(name, subj, crse) VALUES ( 'NETWORKING LABORATORY II', 'CSCI', 4660 );
INSERT IGNORE INTO courses(name, subj, crse) VALUES ( '
WEB SYSTEMS DEVELOPMENT', 'ITWS', 2110 );

/* STUDENTS_COURSES */
INSERT IGNORE INTO students_courses(userId, subj, crse) VALUES (1,'ARTS',1200);
INSERT IGNORE INTO students_courses(userId, subj, crse) VALUES (1,'ARTS',2530);
INSERT IGNORE INTO students_courses(userId, subj, crse) VALUES (1,'CSCI',4660);
INSERT IGNORE INTO students_courses(userId, subj, crse) VALUES (1,'ITWS',2110);

/* TAS_COURSES */
INSERT IGNORE INTO tas_courses(userId,subj,crse) VALUES (2,'ARTS',1200);