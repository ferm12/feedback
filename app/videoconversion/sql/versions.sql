#versions table ties updates in a video

CREATE TABLE versions (
    id int unsigned not null auto_increment PRIMARY KEY,
    video_id int unsigned not null,
    review_stage char(7),
    version_number TINYINT(2) unsigned not null,
    duration FLOAT(10) not null,
    fps FLOAT(6) not null,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT NULL,
    INDEX (video_id),
    FOREIGN KEY (video_id) REFERENCES videos (id)
) ENGINE=INNODB;