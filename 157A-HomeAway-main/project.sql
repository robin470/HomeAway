CREATE DATABASE Company;
USE Company;

CREATE TABLE Project(
  Pnumber INTEGER PRIMARY KEY,
  Pname VARCHAR(20) NOT NULL,
  Plocation VARCHAR(20) NOT NULL

);

INSERT INTO Project VALUES(1, 'project A', 'Home');
INSERT INTO Project VALUES(2, 'project B', 'Room');
INSERT INTO Project VALUES(3, 'project C', 'Cafe');
INSERT INTO Project VALUES(4, 'project D', 'Home');
INSERT INTO Project VALUES(5, 'project E', 'na');
