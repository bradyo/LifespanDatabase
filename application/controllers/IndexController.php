<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction() {
        
    }
    
    
//    public function indexAction()
//    {
//        $db = Zend_Registry::get('db');
//        $stmt = $db->prepare('
//            SELECT o.species as species, COUNT(o.id) as observationCount
//            FROM observation o
//            WHERE o.species IS NOT NULL AND o.status = "public"
//            GROUP BY o.species
//            ORDER BY observationCount DESC
//            ');
//        $stmt->execute();
//        $rows = $stmt->fetchAll();
//
//        $this->view->statistics = array();
//        foreach ($rows as $row) {
//            $this->view->statistics[] = array(
//                'species' => $row['species'], 
//								'short_species' => $this->_getShortSpecies($row['species']),
//                'observationCount' => $row['observationCount'],
//            );
//        }
//
//        // grab growth stats from database
//        $lastDay   = date('Y-m-d H:i:s', time() - 24 * 3600);
//        $lastWeek  = date('Y-m-d H:i:s', time() - 7 * 24 * 3600);
//        $lastMonth = date('Y-m-d H:i:s', mktime(0, 0, 0, date("m")-1, date("d"),   date("Y")));
//        $lastYear  = date('Y-m-d H:i:s', mktime(0, 0, 0, date("m"),   date("d"),   date("Y")-1));
//
//        $stmt = $db->prepare('SELECT COUNT(1) as count FROM observation WHERE created_at > ?');
//        $stmt->execute(array($lastDay));
//        if (($result = $stmt->fetch(PDO::FETCH_ASSOC)) !== null) {
//            $lastDayCount = $result['count'];
//        }
//        $stmt->execute(array($lastWeek));
//        if (($result = $stmt->fetch(PDO::FETCH_ASSOC)) !== null) {
//            $lastWeekCount = $result['count'];
//        }
//        $stmt->execute(array($lastMonth));
//        if (($result = $stmt->fetch(PDO::FETCH_ASSOC)) !== null) {
//            $lastMonthCount = $result['count'];
//        }
//        $stmt->execute(array($lastYear));
//        if (($result = $stmt->fetch(PDO::FETCH_ASSOC)) !== null) {
//            $lastYearCount = $result['count'];
//        }
//        $this->view->growthRows = array(
//            'last 24 hr' => $lastDayCount,
//            'last week' => $lastWeekCount,
//            'last month' => $lastMonthCount,
//            'last year' => $lastYearCount,
//        );
//
//        // get featured observation
//        $stmt = $db->prepare("SELECT observation_id FROM featured_observation");
//        $stmt->execute();
//        $featuredIds = array();
//        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//            $rowId = $row['observation_id'];
//            $featuredIds[$rowId] = $rowId;
//        }
//        $this->view->featuredObservations = array();
//        if (count($featuredIds) > 0) {
//            foreach ($featuredIds as $featuredId) {
//                $featuredObservation = Doctrine_Core::getTable('Observation')
//                    ->getObservation($featuredId);
//
//                $body = $featuredObservation['body'];
//                $body = preg_replace('/\[pubmed:(\d+)\]/',
//                    '<a class="grayed-anchor" href="http://www.ncbi.nlm.nih.gov/pubmed/$1">[pubmed]</a>',
//                    $body);
//                $featuredObservation->body = $body;
//
//                $this->view->featuredObservations[] = $featuredObservation;
//            }
//        }
//
//        // get most recent observation
//        $lastObservation = Doctrine_Core::getTable('Observation')
//            ->getMostRecentObservation();
//        $body = $lastObservation['body'];
//        $body = preg_replace('/\[pubmed:(\d+)\]/',
//            '<a class="grayed-anchor" href="http://www.ncbi.nlm.nih.gov/pubmed/$1">[pubmed]</a>',
//            $body);
//        $lastObservation->body = $body;
//        $this->view->lastObservation = $lastObservation;
//
//        // fetch top contributers
//        $stmt = $db->prepare("
//            SELECT r.requested_by, COUNT(r.observation_id) AS count
//            FROM observation_revision r
//            WHERE r.action = 'create' AND r.status = 'accepted'
//            GROUP BY r.requested_by
//            ORDER BY COUNT(r.observation_id) DESC
//            LIMIT 5
//            ");
//        $stmt->execute();
//        $this->view->topContributers = array();
//        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//            $this->view->topContributers[] = array(
//                'username' => $row['requested_by'],
//                'count' => $row['count'],
//            );
//        }
//    }
//
//    private function _getShortSpecies($species)
//    {
//        if (preg_match('/^([a-z]{1})[a-z]*\s{1}(.+)$/i', $species, $matches)) {
//            return $matches[1].'. '.$matches[2];
//        }
//    }
//
//    public function observationsAction()
//    {
//        $browser = new Application_Model_ObservationBrowser($this->_getAllParams(),
//            $this->_getParam('page', 1));
//        $this->view->form = $browser->getForm();
//        $this->view->rows = $browser->getRows();
//        $this->view->paginator = $browser->getPaginator();
//    }
    
    public function testAction() {
        $observation = new Application_Model_Observation();
        $observation->setBody('test');
        
        $em = Application_Registry::getEm();
        $em->persist($observation);
        $em->flush();
    }
}

