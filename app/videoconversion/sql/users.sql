CREATE TABLE users (
    id int unsigned not null auto_increment PRIMARY KEY,
    activated boolean not null default 0,
    company_id int unsigned not null,
    email varchar(30),
    username varchar(30),
    password char(60), # bcrypt hashing
    first_name varchar(30),
    last_name varchar(30),
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT NULL,
    INDEX (company_id),
    FOREIGN KEY (company_id) REFERENCES companies (id),
    UNIQUE (email),
    UNIQUE (username)
) ENGINE=INNODB;