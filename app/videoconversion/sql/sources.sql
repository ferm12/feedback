CREATE TABLE sources (
    id int unsigned not null auto_increment PRIMARY KEY,
    version_id int unsigned not null,
    master boolean not null default 0,
    format char(5),
    location varchar(50),
    zip varchar(50) DEFAULT null,
    width TINYINT(6),
    height TINYINT(6),
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT NULL,
    INDEX (version_id),
    FOREIGN KEY (version_id) REFERENCES versions (id)
) ENGINE=INNODB;