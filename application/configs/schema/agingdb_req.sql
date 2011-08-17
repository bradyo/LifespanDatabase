CREATE TABLE taxonomy (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(128) NOT NULL,
    common_name VARCHAR(128),
    ncbi_tax_id INT,
    INDEX common_name_index (common_name),
    INDEX name_index (name),
    INDEX ncbi_tax_id_index (ncbi_tax_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE taxonomy_synonym (
    id INT AUTO_INCREMENT PRIMARY KEY,
    taxonomy_id INT NOT NULL,
    type VARCHAR(64),
    synonym VARCHAR(128) NOT NULL,
    INDEX taxonomy_id_index (taxonomy_id),
    INDEX type_index (type),
    INDEX synonym_index (synonym),
    FOREIGN KEY (taxonomy_id) REFERENCES taxonomy (id) ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;


CREATE TABLE compound (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description VARCHAR(255),
    ncbi_compound_id INT,
    INDEX name_index (name),
    INDEX ncbi_compound_id_index (ncbi_compound_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE compound_synonym (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compound_id INT NOT NULL,
    synonym VARCHAR(64) NOT NULL,
    INDEX compound_id_index (compound_id),
    INDEX synonym_index (synonym),
    FOREIGN KEY (compound_id) REFERENCES compound (id) ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;


CREATE TABLE gene (
    id INT AUTO_INCREMENT PRIMARY KEY,
    taxonomy_id INT,
    species VARCHAR(64),
    symbol VARCHAR(64),
    locus_tag VARCHAR(64),
    description VARCHAR(255),
    ncbi_gene_id INT,
    INDEX taxonomy_id_index (taxonomy_id),
    INDEX species_index (species),
    INDEX symbol_index (symbol),
    INDEX locus_tag_index (locus_tag),
    INDEX ncbi_gene_id_index (ncbi_gene_id),
    FOREIGN KEY (taxonomy_id) REFERENCES taxonomy (id) ON DELETE SET NULL
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE gene_synonym (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gene_id INT NOT NULL,
    synonym VARCHAR(64) NOT NULL,
    INDEX gene_id_index (gene_id),
    INDEX synonym_index (synonym),
    FOREIGN KEY (gene_id) REFERENCES gene (id) ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE gene_link (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gene_id INT NOT NULL,
    database_id VARCHAR(64) NOT NULL,
    identifier VARCHAR(64) NOT NULL,
    INDEX gene_id_index (gene_id),
    INDEX database_id_index (database_id),
    INDEX identifier_index (identifier),
    FOREIGN KEY (gene_id) REFERENCES gene (id) ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;



CREATE TABLE gene_go (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gene_id INT NOT NULL,
    go_id VARCHAR(64) NOT NULL,
    evidence VARCHAR(64),
    category VARCHAR(64),
    term VARCHAR(255),

    INDEX gene_id_index (gene_id),
    INDEX go_id_index (go_id),
    INDEX evidence_index (evidence),
    INDEX category_index(category),
    FOREIGN KEY (gene_id) REFERENCES gene (id) ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE gene_homolog (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gene_id INT NOT NULL,
    ncbi_gene_id INT,
    homolog_gene_id INT,
    homolog_ncbi_gene_id INT,
    algorithm VARCHAR(64),
    family VARCHAR(64),
    species VARCHAR(64),
    symbol VARCHAR(64),
    protein_database VARCHAR(64),
    protein_id VARCHAR(64),

    INDEX gene_id_index (gene_id),
    INDEX ncbi_gene_id_index (ncbi_gene_id),
    INDEX homolog_gene_id_index (homolog_gene_id),
    INDEX homolog_ncbi_gene_id_index (homolog_ncbi_gene_id),
    INDEX algorithm_index (algorithm),
    INDEX family_index (family),
    INDEX species_index (species),
    INDEX symbol_index (symbol),
    INDEX protein_database_index (protein_database),
    INDEX protein_id_index (protein_id),

    FOREIGN KEY (gene_id) REFERENCES gene (id) ON DELETE CASCADE,
    FOREIGN KEY (homolog_gene_id) REFERENCES gene (id) ON DELETE SET NULL
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE gene_interaction (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gene_id INT NOT NULL,
    name VARCHAR(64) NOT NULL,
    source VARCHAR(128),
    pubmed_id INT,
    partner_gene_id INT,

    INDEX gene_id_index (gene_id),
    INDEX name_index (name),
    INDEX source_index (source),
    INDEX pubmed_id_index (pubmed_id),
    INDEX partner_gene_id_index (partner_gene_id),
    FOREIGN KEY (gene_id) REFERENCES gene (id) ON DELETE CASCADE,
    FOREIGN KEY (partner_gene_id) REFERENCES gene (id) ON DELETE SET NULL
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE gene_substrate (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gene_id INT NOT NULL,
    name VARCHAR(64) NOT NULL,
    ncbi_compound_id INT,
    compound_id INT,

    INDEX gene_id_index (gene_id),
    INDEX name_index (name),
    INDEX ncbi_compound_id_index (ncbi_compound_id),
    INDEX compound_id_index (compound_id),
    FOREIGN KEY (gene_id) REFERENCES gene (id) ON DELETE CASCADE,
    FOREIGN KEY (compound_id) REFERENCES compound (id) ON DELETE SET NULL
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;


CREATE TABLE citation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year INT,
    author VARCHAR(255),
    title VARCHAR(255),
    source VARCHAR(255),
    pubmed_id INT,

    INDEX year_index (year),
    INDEX author_index (author),
    INDEX title_index (title),
    INDEX source_index (source),
    INDEX pubmed_id_index (pubmed_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

