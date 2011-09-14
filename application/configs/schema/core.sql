
CREATE TABLE IF NOT EXISTS user (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(32) UNIQUE NOT NULL,
    name VARCHAR(64),
    email VARCHAR(128),
    role VARCHAR(32) DEFAULT "member",
    status VARCHAR(32) DEFAULT "active",
    created_at DATETIME,
    seen_at DATETIME,
    password_algorithm VARCHAR(32),
    password_hash VARCHAR(40),
    password_salt VARCHAR(40),
    activation_key VARCHAR(40),
    timezone VARCHAR(32) NOT NULL DEFAULT "America/Los_Angeles",
    locale VARCHAR(32) NOT NULL DEFAULT "en_US",
    language VARCHAR(2) NOT NULL DEFAULT "en",
    newsletter VARCHAR(32) NULL,
    reputation INTEGER NOT NULL DEFAULT 1,
    post_count INTEGER NOT NULL DEFAULT 0,
    INDEX(name), INDEX (role), INDEX (status), INDEX (email),
    INDEX (timezone), INDEX (locale), INDEX (language), INDEX (newsletter),
    INDEX (created_at), INDEX (seen_at), INDEX (reputation), INDEX (post_count)
) ENGINE = INNODB DEFAULT CHARSET = utf8;


CREATE TABLE citation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year INT,
    author TEXT,
    title TEXT,
    source TEXT,
    pubmed_id INT,
    INDEX (year),
    INDEX (pubmed_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE species (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guid CHAR(32) NOT NULL,
    name VARCHAR(128) NOT NULL,
    common_name VARCHAR(128),
    ncbi_tax_id INT,    
    INDEX (guid),
    INDEX (common_name),
    INDEX (name),
    INDEX (ncbi_tax_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE species_synonym (
    id INT AUTO_INCREMENT PRIMARY KEY,
    species_id INT NOT NULL,
    type VARCHAR(64),
    synonym VARCHAR(128) NOT NULL,
    FOREIGN KEY (species_id) REFERENCES species (id) ON DELETE CASCADE
    INDEX (type),
    INDEX (synonym)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE strain (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guid CHAR(32) NOT NULL,
    species_id INT,
    name VARCHAR(128) NOT NULL,
    common_name VARCHAR(128),
    INDEX (guid),
    FOREIGN KEY (species_id) REFERENCES species (id) ON DELETE SET NULL
    INDEX (name),
    INDEX (common_name)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;


CREATE TABLE compound (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guid CHAR(32) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description VARCHAR(255),
    ncbi_compound_id INT,
    INDEX (guid),
    INDEX (ncbi_compound_id),
    INDEX (name)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE compound_synonym (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compound_id INT NOT NULL,
    synonym VARCHAR(64) NOT NULL,
    INDEX (compound_id),
    INDEX (synonym),
    FOREIGN KEY (compound_id) REFERENCES compound (id) ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;


CREATE TABLE gene (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guid CHAR(32) NOT NULL,
    species_id INT,
    species VARCHAR(64),
    symbol VARCHAR(64),
    locus_tag VARCHAR(64),
    description VARCHAR(255),
    ncbi_gene_id INT,
    INDEX (guid),
    FOREIGN KEY (species_id) REFERENCES species (id) ON DELETE SET NULL
    INDEX (symbol),
    INDEX (locus_tag),
    INDEX (ncbi_gene_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE gene_synonym (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gene_id INT NOT NULL,
    synonym VARCHAR(64) NOT NULL,
    INDEX (gene_id),
    INDEX (synonym),
    FOREIGN KEY (gene_id) REFERENCES gene (id) ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE gene_link (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gene_id INT NOT NULL,
    database_id VARCHAR(64) NOT NULL,
    identifier VARCHAR(64) NOT NULL,
    INDEX (gene_id),
    INDEX (database_id),
    INDEX (identifier),
    FOREIGN KEY (gene_id) REFERENCES gene (id) ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE gene_go (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gene_id INT NOT NULL,
    go_id VARCHAR(64) NOT NULL,
    evidence VARCHAR(64),
    category VARCHAR(64),
    term VARCHAR(255),
    INDEX (gene_id),
    INDEX (go_id),
    INDEX (evidence),
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
    INDEX (gene_id),
    INDEX (ncbi_gene_id),
    INDEX (homolog_gene_id),
    INDEX (homolog_ncbi_gene_id),
    INDEX (algorithm),
    INDEX (family),
    INDEX (species),
    INDEX (symbol),
    INDEX (protein_database),
    INDEX (protein_id),
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
    INDEX (gene_id),
    INDEX (name),
    INDEX (source),
    INDEX (pubmed_id),
    INDEX (partner_gene_id),
    FOREIGN KEY (gene_id) REFERENCES gene (id) ON DELETE CASCADE,
    FOREIGN KEY (partner_gene_id) REFERENCES gene (id) ON DELETE SET NULL
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE gene_substrate (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gene_id INT NOT NULL,
    name VARCHAR(64) NOT NULL,
    ncbi_compound_id INT,
    compound_id INT,
    INDEX (gene_id),
    INDEX (name),
    INDEX (ncbi_compound_id),
    INDEX (compound_id),
    FOREIGN KEY (gene_id) REFERENCES gene (id) ON DELETE CASCADE,
    FOREIGN KEY (compound_id) REFERENCES compound (id) ON DELETE SET NULL
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;


CREATE TABLE comment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_guid CHAR(32) NOT NULL,
    status VARCHAR(64) NOT NULL,
    authored_at DATETIME,
    author_id INT,
    review_status VARCHAR(64),
    reviewed_at DATETIME,
    reviewer_id INT,
    author_name VARCHAR(255),
    author_email VARCHAR(255),
    body TEXT,
    INDEX (parent_guid),
    INDEX (status),
    INDEX (review_status),
    INDEX (authored_at),
    FOREIGN KEY (author_id) REFERENCES `user` (id) ON DELETE SET NULL,
    INDEX (reviewed_at),
    FOREIGN KEY (reviewer_id) REFERENCES `user` (id) ON DELETE SET NULL
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;




CREATE TABLE observation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guid CHAR(32) NOT NULL,
    public_id INTEGER NOT NULL,
    version INTEGER,
    status VARCHAR(64),
    authored_at DATETIME,
    author_id INT,
    review_status VARCHAR(64),
    reviewed_at DATETIME,
    reviewer_id INT,
    reviewer_comment TEXT,

    created_at DATETIME,
    updated_at DATETIME,
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

    INDEX (public_id, version),
    INDEX (public_id),
    INDEX (version),
    INDEX (status),
    FOREIGN KEY (author_id) REFERENCES `user` (id) ON DELETE SET NULL,
    INDEX (authored_at), 
    INDEX (review_status),
    INDEX (reviewed_at),
    FOREIGN KEY (reviewer_id) REFERENCES `user` (id) ON DELETE SET NULL,
    INDEX (created_at),
    INDEX (updated_at), 
    INDEX (lifespan),
    INDEX (lifespan_base),
    INDEX (lifespan_units),
    INDEX (lifespan_change),
    INDEX (lifespan_effect),
    INDEX (lifespan_measure),
    FOREIGN KEY (taxonomy_id) REFERENCES taxonomy (id) ON DELETE SET NULL,
    INDEX (species),
    INDEX (strain),
    INDEX (cell_type),
    INDEX (mating_type),
    INDEX (temperature),
    INDEX (citation_pubmed_id),
    INDEX (citation_year),
    INDEX (citation_author),
    INDEX (citation_title),
    INDEX (citation_source)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE observation_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    observation_id INT NOT NULL,
    gene_count INT NOT NULL DEFAULT(0),
    compound_count INT NOT NULL DEFAULT (0),
    environment_count INT NOT NULL DEFAULT (0),
    FOREIGN KEY (observation_id) REFERENCES observation (id) ON DELETE CASCADE,
    INDEX (gene_count),
    INDEX (compound_count),
    INDEX (environment_count)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;


CREATE TABLE observation_gene (
    id INT AUTO_INCREMENT PRIMARY KEY,
    observation_id INT NOT NULL,
    gene_id INT,
    allele_type VARCHAR(64),
    allele VARCHAR(64),
    FOREIGN KEY (observation_id) REFERENCES observation (id) ON DELETE CASCADE,
    FOREIGN KEY (gene_id) REFERENCES gene (id) ON DELETE SET NULL,    
    INDEX (allele_type),
    INDEX (allele)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE observation_compound (
    id INT AUTO_INCREMENT PRIMARY KEY,
    observation_id INT NOT NULL,
    compound_id INT,
    quantity VARCHAR(64),
    FOREIGN KEY (observation_id) REFERENCES observation (id) ON DELETE CASCADE,
    FOREIGN KEY (compound_id) REFERENCES compound (id) ON DELETE SET NULL,
    INDEX (quantity)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE observation_environment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    observation_id INT NOT NULL,
    type VARCHAR(64) NOT NULL,
    body TEXT NOT NULL,
    FOREIGN KEY (observation_id) REFERENCES observation (id) ON DELETE CASCADE,
    INDEX (type)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;


CREATE TABLE featured_observation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    observation_public_id INT NOT NULL,
    position INT DEFAULT(0),
    INDEX (observation_public_id),
    INDEX (position)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
