CREATE TABLE "users" (
	"id"	INTEGER,
	"level"	INTEGER NOT NULL DEFAULT 0,
	"email"	VARCHAR(255) NOT NULL,
	"password"	VARCHAR(255) NOT NULL,
	"data"	TEXT,
	"token" VARCHAR(255),
	"created"	DATETIME,
	"modified"	DATETIME,
	PRIMARY KEY("id" AUTOINCREMENT)
);

insert INTO "users" ("level", "email", "password") VALUES (2, "draconigen@gmail.com", "0");