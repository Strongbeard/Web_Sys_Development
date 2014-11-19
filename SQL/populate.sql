USE TA_Hunter;

/* USERS */
INSERT IGNORE INTO users (userid, email, isAdmin, isStudent, isTA, isTutor) VALUES (1,'mahonk3@rpi.edu',FALSE,TRUE,FALSE,FALSE);

/* PASSWORDS */
INSERT IGNORE INTO passwords(userid, password) VALUES (1,'$2y$10$KN0.2dx2J7ZCj1aPqhruwuZ7YR0soWcv/rWX7FuURB2U9EC5wUe2u');

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