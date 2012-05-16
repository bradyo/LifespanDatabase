CREATE TABLE comment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_table VARCHAR(128) NOT NULL,
    parent_id INT NOT NULL,
    author VARCHAR(64),
    created_at DATETIME,
    body TEXT,
    status VARCHAR(64) NOT NULL,

    INDEX parent_table_index (parent_table),
    INDEX parent_id_index (parent_id),
    INDEX author_index (author),
    INDEX created_at_index (created_at),
    INDEX status_index (status)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE comment_search (
    id INT AUTO_INCREMENT PRIMARY KEY,
    body TEXT,
    FULLTEXT (body)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = MyISAM;

CREATE TABLE comment_link (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comment_id INT NOT NULL,
    type VARCHAR(64) NOT NULL,
    identifier VARCHAR(64) NOT NULL,
    INDEX comment_id_index (comment_id),
    INDEX type_index (type),
    INDEX identifier_index (identifier)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE featured_observation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    observation_id INT NOT NULL,
    INDEX observation_id_index (observation_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
