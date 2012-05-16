<?php

class Application_View_Helper_SpeciesHelper extends Zend_View_Helper_Abstract
{

    public function getShortSpecies($species)
    {
        if (preg_match('/^([a-z]{1})[a-z]*\s{1}(.+)$/i', $species, $matches)) {
            return $matches[1].'. '.$matches[2];
        }
    }
}