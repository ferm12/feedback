CREATE TABLE drawings (
    id int unsigned not null auto_increment PRIMARY KEY,
    feedback_id int unsigned not null,
    svg TEXT,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT NULL,
    INDEX (feedback_id),
    FOREIGN KEY (feedback_id) REFERENCES feedback (id)
) ENGINE=INNODB;