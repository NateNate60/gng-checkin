DROP TABLE IF EXISTS EventAttendance;
DROP TABLE IF EXISTS Players;
DROP TABLE IF EXISTS Events;

CREATE TABLE Players (
	pid INTEGER(16) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	fname VARCHAR(256) NOT NULL,
	lname VARCHAR(256) NOT NULL,
	phone CHAR(10) NOT NULL,
	email VARCHAR(256),
	bday CHAR(10) NOT NULL,
	parent_name VARCHAR(256),
	pokemon_id VARCHAR(256),
	mha_id VARCHAR(256),
	mtg_id VARCHAR(256)
);

CREATE TABLE Events (
	event_type INTEGER(10) AUTO_INCREMENT,
	event_name VARCHAR(256),
	PRIMARY KEY (event_type)
);

CREATE TABLE EventAttendance (
	pid INTEGER(16) NOT NULL,
	event_date CHAR(10),
	event_type INTEGER(10),
	PRIMARY KEY (pid, event_date, event_type),
	FOREIGN KEY (pid) REFERENCES Players(pid),
	FOREIGN KEY (event_type) REFERENCES Events(event_type)
);

