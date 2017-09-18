CREATE TABLE projects (
    id int unsigned not null auto_increment PRIMARY KEY,
    name varchar(50),
    company_id int unsigned not null,
    invoiced boolean not null default 0,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT NULL,
    INDEX (company_id),
    FOREIGN KEY (company_id) REFERENCES companies (id),
    UNIQUE (name)
) ENGINE=INNODB;