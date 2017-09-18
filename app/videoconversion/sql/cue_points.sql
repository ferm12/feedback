CREATE TABLE cue_points (
    id int unsigned not null auto_increment PRIMARY KEY,
    version_id int unsigned not null,
    in_point FLOAT(10) not null,
    out_point FLOAT(10) DEFAULT null,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT NULL,
    INDEX (version_id),
    FOREIGN KEY (version_id) REFERENCES versions (id)
) ENGINE=INNODB;