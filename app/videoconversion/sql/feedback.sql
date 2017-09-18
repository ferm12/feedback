CREATE TABLE feedback (
    id int unsigned not null auto_increment PRIMARY KEY,
    cue_point_id int unsigned not null,
    user_id int unsigned not null,
    note TEXT,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT NULL,
    INDEX (cue_point_id),
    INDEX (user_id),
    FOREIGN KEY (cue_point_id) REFERENCES cue_points (id),
    FOREIGN KEY (user_id) REFERENCES users (id)
) ENGINE=INNODB;