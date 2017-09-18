CREATE TABLE videos (
    id int unsigned not null auto_increment PRIMARY KEY,
    title varchar(50),
    description text,
    project_id int unsigned not null,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT NULL,
    INDEX (project_id),
    FOREIGN KEY (project_id) REFERENCES projects (id)
) ENGINE=INNODB;