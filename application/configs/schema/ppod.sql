
CREATE TABLE homolog (
    id INT AUTO_INCREMENT,
	family_id VARCHAR(64) NOT NULL,
	algorithm VARCHAR(64) NOT NULL,
    database_id VARCHAR(64) NOT NULL,
    protein_id VARCHAR(64) NOT NULL,

    PRIMARY KEY (id),
	INDEX family_id_index (family_id),
    INDEX algorithm_index (algorithm),
    INDEX database_id_index (database_id),
    INDEX protein_id_index (protein_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
