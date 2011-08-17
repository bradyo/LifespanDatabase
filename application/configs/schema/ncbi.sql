
CREATE TABLE taxon (
	taxon_id INT NOT NULL,
	name VARCHAR(128) NOT NULL,
	INDEX name_index_idx (name),
	PRIMARY KEY(taxon_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE taxon_synonym (
	id INT AUTO_INCREMENT,
	taxon_id INT NOT NULL,
	synonym VARCHAR(128) NOT NULL,
	class VARCHAR(64),
	INDEX taxon_index_idx (taxon_id),
	INDEX synonym_index_idx (synonym),
	PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;


CREATE TABLE gene (
	gene_id INT NOT NULL,
	taxon_id INT,
	symbol VARCHAR(64),
	locus_tag VARCHAR(64),
    description VARCHAR(255),
	protein_id INT,
    protein_acc VARCHAR(64),
	INDEX taxon_id_index_idx (taxon_id),
	INDEX symbol_index_idx (symbol),
	INDEX locus_tag_index_idx (locus_tag),
	INDEX protein_id_index_idx (protein_id),
    INDEX protein_acc_index_idx (protein_acc),
	PRIMARY KEY(gene_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE gene_dbxref (
	id INT AUTO_INCREMENT,
	gene_id INT NOT NULL,
	dbxref VARCHAR(64) NOT NULL,
	INDEX gene_id_index_idx (gene_id),
	INDEX dbxref_index_idx (dbxref),
	PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE gene_synonym (
	id INT AUTO_INCREMENT,
	gene_id INT NOT NULL,
	synonym VARCHAR(64) NOT NULL,
	INDEX gene_id_index_idx (gene_id),
	INDEX synonym_index_idx (synonym),
	PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;


CREATE TABLE gene_go (
	gene_id INT NOT NULL,
	go_id VARCHAR(64) NOT NULL,
	evidence VARCHAR(64) NOT NULL,
	category VARCHAR(64) NOT NULL,
	term VARCHAR(255) NOT NULL,
	INDEX gene_id_index_idx (gene_id),
	INDEX go_id_index_idx (go_id),
	INDEX evidence_index_idx (evidence),
	INDEX category_index_idx (category)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE gene_uniprot (
	gene_id INT NOT NULL,
	uniprot_id VARCHAR(64) NOT NULL,
	INDEX gene_id_index_idx (gene_id),
	INDEX uniprot_id_index_idx (uniprot_id),
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;



