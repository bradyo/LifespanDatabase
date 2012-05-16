
CREATE TABLE observation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lifespan DOUBLE,
    lifespan_base DOUBLE,
    lifespan_units VARCHAR(64),
    lifespan_change DOUBLE,
    lifespan_effect VARCHAR(64),
    lifespan_measure VARCHAR(64),
    taxonomy_id INT,
    species VARCHAR(64),
    strain VARCHAR(64),
    cell_type VARCHAR(64),
    mating_type VARCHAR(64),
    temperature DECIMAL(5,2),
    citation_pubmed_id INT,
    citation_year INT,
    citation_author VARCHAR(255),
    citation_title VARCHAR (255),
    citation_source VARCHAR (255),
    body TEXT,
    gene_count INT NOT NULL DEFAULT(0),
    compound_count INT NOT NULL DEFAULT (0),
    environment_count INT NOT NULL DEFAULT (0),
    created_at DATETIME,
    updated_at DATETIME,
    status VARCHAR(64),

    INDEX lifespan_index (lifespan),
    INDEX lifespan_base_index (lifespan_base),
    INDEX lifespan_units_index (lifespan_units),
    INDEX lifespan_change_index (lifespan_change),
    INDEX lifespan_effect_index (lifespan_effect),
    INDEX lifespan_measure_index (lifespan_measure),
    INDEX taxonomy_id_index (taxonomy_id),
    INDEX species_index (species),
    INDEX strain_index (strain),
    INDEX cell_type_index (cell_type),
    INDEX mating_type_index (mating_type),
    INDEX temperature_index (temperature),
    INDEX citation_pubmed_id_index (citation_pubmed_id),
    INDEX citation_year_index (citation_year),
    INDEX citation_author_index (citation_author),
    INDEX citation_title_index (citation_title),
    INDEX citation_source_index (citation_source),
    INDEX gene_count_index (gene_count),
    INDEX compound_count_index (compound_count),
    INDEX environment_count_index (environment_count),
    INDEX created_at_index (created_at),
    INDEX updated_at_index (updated_at),
    INDEX status_index (status),
    FOREIGN KEY (taxonomy_id) REFERENCES taxonomy (id) ON DELETE SET NULL
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE observation_search (
    id INT AUTO_INCREMENT PRIMARY KEY,
    body TEXT,
    FULLTEXT (body)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = MyISAM;

CREATE TABLE observation_gene (
    id INT AUTO_INCREMENT PRIMARY KEY,
    observation_id INT NOT NULL,
    gene_id INT,
    ncbi_gene_id INT,
    symbol VARCHAR(64) NOT NULL,
    allele_type VARCHAR(64),
    allele VARCHAR(64),

    INDEX observation_id_index (observation_id),
    INDEX symbol_index (symbol),
    INDEX allele_type_index (allele_type),
    INDEX allele_index (allele),
    INDEX gene_id_index (gene_id),
    INDEX ncbi_gene_id_index (ncbi_gene_id),
    FOREIGN KEY (observation_id) REFERENCES observation (id) ON DELETE CASCADE,
    FOREIGN KEY (gene_id) REFERENCES gene (id) ON DELETE SET NULL
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE observation_compound (
    id INT AUTO_INCREMENT PRIMARY KEY,
    observation_id INT NOT NULL,
    compound_id INT,
    ncbi_compound_id INT,
    name VARCHAR(64) NOT NULL,
    quantity VARCHAR(64),

    INDEX observation_id_index (observation_id),
    INDEX compound_id_index (compound_id),
    INDEX ncbi_compound_id_index (ncbi_compound_id),
    INDEX name_index (name),
    INDEX quantity_index (quantity),
    FOREIGN KEY (observation_id) REFERENCES observation (id) ON DELETE CASCADE,
    FOREIGN KEY (compound_id) REFERENCES compound (id) ON DELETE SET NULL
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE observation_environment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    observation_id INT NOT NULL,
    type VARCHAR(64) NOT NULL,
    body TEXT NOT NULL,

    INDEX observation_id_index (observation_id),
    INDEX type_index (type),
    FOREIGN KEY (observation_id) REFERENCES observation (id) ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE observation_environment_search (
    id INT AUTO_INCREMENT PRIMARY KEY,
    body TEXT,
    FULLTEXT (body)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = MyISAM;


CREATE TABLE observation_revision (
    id INT AUTO_INCREMENT PRIMARY KEY,
    observation_id INT,
    action VARCHAR(64),
    observation_data TEXT NOT NULL,
    requested_by VARCHAR(64),
    requested_at DATETIME,
    reviewed_by VARCHAR(64),
    reviewed_at DATETIME,
    reviewer_comment TEXT,
    status VARCHAR(64),

    INDEX observation_id_index (observation_id),
    INDEX action_index (action),
    INDEX requested_by_index (requested_by),
    INDEX requested_at_index (requested_at),
    INDEX reviewed_by_index (reviewed_by),
    INDEX reviewed_at_index (reviewed_at),
    INDEX status_index (status),
    FOREIGN KEY (observation_id) REFERENCES observation (id) ON DELETE SET NULL
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
