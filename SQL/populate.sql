USE TA_Hunter;

/* USERS */
INSERT IGNORE INTO users (userid, email, isAdmin, isStudent, isTA, isTutor, firstName, lastName) VALUES (1,'mahonk3@rpi.edu',FALSE,TRUE,FALSE,FALSE,'Kevin','Mahon'); /* password = mahonk3 */
INSERT IGNORE INTO users (userid, email, isAdmin, isStudent, isTA, isTutor, firstName, lastName) VALUES (2,'ta@test.com',FALSE,FALSE,TRUE,FALSE,'TA_First','TA_Last'); /* password = testtesttest */

/* PASSWORDS */
INSERT IGNORE INTO passwords(userid, password) VALUES (1,'$2y$10$KN0.2dx2J7ZCj1aPqhruwuZ7YR0soWcv/rWX7FuURB2U9EC5wUe2u');
INSERT IGNORE INTO passwords(userid, password) VALUES (2,'$2y$10$upW9J0/LbrG07GQYFWVYfuObnwX5TlRWHFx0YHgTpQqhNBI3bRmvK');

/* COURSES */
INSERT IGNORE INTO courses(name, subj, crse, ta_code) VALUES ( 'BASIC DRAWING', 'ARTS', 1200, '30aggahfngg4ccmp8engi3v3l75zs9m4x5m0ntfe5ouppd6cny' );
INSERT IGNORE INTO courses(name, subj, crse, ta_code) VALUES ( 'ART HISTORY I:FROM PALEOLITHIC TO RENAISSANCE', 'ARTS', 2530, '4wvro8maf69fyfebobv6zqmvhjnz50p9gnw0uwkvnze8xabuof' );
INSERT IGNORE INTO courses(name, subj, crse, ta_code) VALUES ( 'NETWORKING LABORATORY II', 'CSCI', 4660, 'tmtf2u0l9sut0q2xkscy1nal2mjqd9eubjmocd5uqlt9ao0nzu' );
INSERT IGNORE INTO courses(name, subj, crse, ta_code) VALUES ( '
WEB SYSTEMS DEVELOPMENT', 'ITWS', 2110, 'ip9yyrw4nmi42dzaishuckivkzov74kt8l6hbtldcu8mfzva03' );

/* STUDENTS_COURSES */
INSERT IGNORE INTO students_courses(userId, subj, crse) VALUES (1,'ARTS',1200);
INSERT IGNORE INTO students_courses(userId, subj, crse) VALUES (1,'ARTS',2530);
INSERT IGNORE INTO students_courses(userId, subj, crse) VALUES (1,'CSCI',4660);
INSERT IGNORE INTO students_courses(userId, subj, crse) VALUES (1,'ITWS',2110);

/* TAS_COURSES */
INSERT IGNORE INTO tas_courses(userId,subj,crse) VALUES (2,'ARTS',1200);