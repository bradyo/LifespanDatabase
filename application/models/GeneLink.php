<?php

/**
 * @Entity
 * @Table(name="gene_link")
 */
class Application_Model_GeneLink
{  
    /**
     * @var integer
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Application_Model_Gene
     * @ManyToOne(targetEntity="Application_Model_Gene", inversedBy="links")
     * @JoinColumn(name="gene_id", referencedColumnName="id")
     */
    private $gene;
        
    /**
     * @var string Database namespace.
     * @Column(name="database_id", type="string", length="64")
     */
    private $linkDatabase;
    
    /**
     * @var string Database dependent unique identifier to link to.
     * @Column(name="identifier", type="string", length="64")
     */
    private $linkId;
    
    
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getGene() {
        return $this->gene;
    }

    public function setGene($gene) {
        $this->gene = $gene;
    }

    public function getLinkDatabase() {
        return $this->linkDatabase;
    }

    public function setLinkDatabase($linkDatabase) {
        $this->linkDatabase = $linkDatabase;
    }

    public function getLinkId() {
        return $this->identifier;
    }

    public function setLinkId($id) {
        $this->linkId = $id;
    }

    /**
     * Gets the absolute link to the proper database page.
     */
    public function getHref() {
        switch ($this->linkDatabase) {
            case 'SGD':         return self::getSgdHref($this->linkId);
            case 'Ensembl':     return self::getEnsemblHref($this->linkId);
            case 'FLYBASE':     return self::getFlybaseHref($this->linkId);
            case 'HGNC':        return self::getHgncHref($this->linkId);
            case 'HPRD':        return self::getHprdHref($this->linkId);
            case 'MGI':         return self::getMgiHref($this->linkId);
            case 'MIM':         return self::getMimHref($this->linkId);
            case 'RGD':         return self::getRgdHref($this->linkId);
            case 'WormBase':    return self::getWormBaseHref($this->linkId);
        }
        return '';
    }
    
    public static function getSgdHref($id) {
        return 'http://www.yeastgenome.org/cgi-bin/locus.fpl?locus='.$id;
    }
    
    public static function getEnsemblHref($id) {
        // Ensembl Urls are different depending on id type
        if (preg_match('/^ENSG\d+$/', $id)) {
            return 'http://www.ensembl.org/Homo_sapiens/Gene/Summary?g='.$id;
        } 
        else if (preg_match('/^ENSMUSG\d+$/', $id)) {
            return 'http://www.ensembl.org/Mus_musculus/Gene/Summary?db=core;g='.$id;
        } 
        else {
            return 'http://www.ebi.ac.uk/ebisearch/search.ebi?db=allebi&query='.$id;
        }
    }
    
    public static function getFlybaseHref($id) {
        return 'http://flybase.org/reports/'.$id .'.html';
    }
    
    public static function getHgncHref($id) {
        return 'http://www.genenames.org/data/hgnc_data.php?hgnc_id='.$id;
    }
    
    public static function getHprdHref($id) {
        return 'http://www.hprd.org/protein/'.$id;
    }
    
    public static function getMgiHref($id) {
        return 'http://www.informatics.jax.org/searches/accession_report.cgi?id='.$id;
    }
    
    public static function getMimHref($id) {
        return 'http://www.ncbi.nlm.nih.gov/omim/'.$id;
    }
    
    public static function getRgdHref($id) {
        return 'http://rgd.mcw.edu/objectSearch/qtlReport.jsp?rgd_id='.$id;
    }
    
    public static function getWormBaseHref($id) {
        return 'http://www.wormbase.org/db/gene/gene?name='.$id.';class=Gene';
    }
}
