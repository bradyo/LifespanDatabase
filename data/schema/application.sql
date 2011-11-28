
CREATE TABLE IF NOT EXISTS session (
    id char(32) PRIMARY KEY,
    modified int,
    lifetime int,
    data text
) ENGINE = INNODB DEFAULT CHARSET = utf8;


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


CREATE TABLE IF NOT EXISTS citation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year INT,
    author TEXT,
    title TEXT,
    source TEXT,
    pubmed_id INT,
    INDEX (year),
    INDEX (pubmed_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS species (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status VARCHAR(32),
    guid CHAR(36) NOT NULL,
    name VARCHAR(128) NOT NULL,
    common_name VARCHAR(128),
    ncbi_tax_id INT,    
    INDEX (status),
    INDEX (guid),
    INDEX (common_name),
    INDEX (name),
    INDEX (ncbi_tax_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS species_synonym (
    id INT AUTO_INCREMENT PRIMARY KEY,
    species_id INT NOT NULL,
    type VARCHAR(64),
    name VARCHAR(128) NOT NULL,
    FOREIGN KEY (species_id) REFERENCES species (id) ON DELETE CASCADE,
    INDEX (type),
    INDEX (name)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS strain (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status VARCHAR(32),
    guid CHAR(36) NOT NULL,
    species_id INT,
    name VARCHAR(128) NOT NULL,
    common_name VARCHAR(128),
    INDEX (status),
    INDEX (guid),
    FOREIGN KEY (species_id) REFERENCES species (id) ON DELETE SET NULL,
    INDEX (name),
    INDEX (common_name)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS compound (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status VARCHAR(32),
    guid CHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description VARCHAR(255),
    ncbi_compound_id INT,
    INDEX (status),
    INDEX (guid),
    INDEX (ncbi_compound_id),
    INDEX (name)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS compound_synonym (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compound_id INT NOT NULL,
    name VARCHAR(64) NOT NULL,
    INDEX (compound_id),
    INDEX (name),
    FOREIGN KEY (compound_id) REFERENCES compound (id) ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS gene (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status VARCHAR(32),
    guid CHAR(36) NOT NULL,
    species VARCHAR(128),
    symbol VARCHAR(64),
    locus_tag VARCHAR(64),
    description VARCHAR(255),
    ncbi_gene_id INT,
    ncbi_protein_id INT,
    INDEX (status),
    INDEX (guid),
    INDEX (species),
    INDEX (symbol),
    INDEX (locus_tag),
    INDEX (ncbi_gene_id),
    INDEX (ncbi_protein_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS gene_synonym (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gene_id INT NOT NULL,
    name VARCHAR(64) NOT NULL,
    INDEX (gene_id),
    INDEX (name),
    FOREIGN KEY (gene_id) REFERENCES gene (id) ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS gene_link (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gene_id INT NOT NULL,
    database_id VARCHAR(64) NOT NULL,
    identifier VARCHAR(64) NOT NULL,
    INDEX (gene_id),
    INDEX (database_id),
    INDEX (identifier),
    FOREIGN KEY (gene_id) REFERENCES gene (id) ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS gene_go (
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

CREATE TABLE IF NOT EXISTS gene_homolog (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gene_id INT NOT NULL,
    homolog_gene_id INT,
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

CREATE TABLE IF NOT EXISTS gene_interaction (
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

CREATE TABLE IF NOT EXISTS gene_substrate (
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


CREATE TABLE IF NOT EXISTS environment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status VARCHAR(32),
    guid CHAR(36) NOT NULL,
    name VARCHAR(128),
    INDEX (status),
    INDEX (guid),
    INDEX (name)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS comment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_guid CHAR(36) NOT NULL,
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




CREATE TABLE IF NOT EXISTS observation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guid CHAR(36) NOT NULL,
    public_id INTEGER NOT NULL,
    version INTEGER,
    is_current TINYINT(1),
    status VARCHAR(32),
    review_status VARCHAR(32),
    authored_at DATETIME,
    author_id INT,
    author_comment TEXT,
    reviewed_at DATETIME,
    reviewer_id INT,
    reviewer_comment TEXT,

    created_at DATETIME,
    correspondance_email VARCHAR(128),
    lifespan DOUBLE,
    lifespan_base DOUBLE,
    lifespan_units VARCHAR(64),
    lifespan_change DOUBLE,
    lifespan_effect VARCHAR(64),
    lifespan_measure VARCHAR(64),
    species VARCHAR(128),
    strain VARCHAR(128),
    cell_type VARCHAR(64),
    mating_type VARCHAR(64),
    temperature DECIMAL(5,2),
    citation_id INT,
    description TEXT,

    gene_count INT NOT NULL DEFAULT 0,
    compound_count INT NOT NULL DEFAULT 0,
    environment_count INT NOT NULL DEFAULT 0,

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
    INDEX (lifespan),
    INDEX (lifespan_base),
    INDEX (lifespan_units),
    INDEX (lifespan_change),
    INDEX (lifespan_effect),
    INDEX (lifespan_measure),
    INDEX (species),
    INDEX (strain),
    INDEX (cell_type),
    INDEX (mating_type),
    INDEX (temperature),
    FOREIGN KEY (citation_id) REFERENCES citation (id) ON DELETE SET NULL,
    INDEX (gene_count),FOREIGN KEY (citation_id) REFERENCES citation (id) ON DELETE SET NULL,
    INDEX (compound_count),
    INDEX (environment_count)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS observation_gene (
    id INT AUTO_INCREMENT PRIMARY KEY,
    observation_id INT NOT NULL,
    gene_id INT,
    symbol VARCHAR(64),
    allele_type VARCHAR(64),
    allele VARCHAR(64),
    FOREIGN KEY (observation_id) REFERENCES observation (id) ON DELETE CASCADE,
    FOREIGN KEY (gene_id) REFERENCES gene (id) ON DELETE SET NULL,
    INDEX (symbol),
    INDEX (allele_type),
    INDEX (allele)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS observation_compound (
    id INT AUTO_INCREMENT PRIMARY KEY,
    observation_id INT NOT NULL,
    compound_id INT,
    name VARCHAR(128),
    quantity VARCHAR(64),
    FOREIGN KEY (observation_id) REFERENCES observation (id) ON DELETE CASCADE,
    FOREIGN KEY (compound_id) REFERENCES compound (id) ON DELETE SET NULL,
    INDEX (name),
    INDEX (quantity)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS observation_environment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    observation_id INT NOT NULL,
    environment_id INT,
    name VARCHAR(64),
    description TEXT,
    FOREIGN KEY (observation_id) REFERENCES observation (id) ON DELETE CASCADE,
    FOREIGN KEY (environment_id) REFERENCES environment (id) ON DELETE SET NULL,
    INDEX (name)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS featured_observation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    observation_id INT NOT NULL,
    position INT DEFAULT 0,
    FOREIGN KEY (observation_id) REFERENCES observation (id) ON DELETE CASCADE,
    INDEX (position)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;