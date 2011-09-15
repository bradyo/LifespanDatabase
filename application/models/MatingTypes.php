<?php

class Application_Model_MatingTypes {
    
    public function getOptions($speciesName = null) {
        if ($speciesName == 'mammal') {
            return $this->getMammalChoices();
        }
        if ($speciesName == 'worm') {
            return $this->getWormChoices();
        }
        if ($speciesName == 'fly') {
            return $this->getFlyChoices();
        }
        return array(
            'male' => 'Male',
            'female' => 'Female',
            'hermaphrodite' => 'Hermaphrodite',
            'mata' => 'MATa',
            'matalpha' => 'MATalpha',
            'diploid' => 'Diploid',
        );
    }
     
    public function getMammalChoices() {
        return array(
            'male' => 'Male',
            'female' => 'Female',
        );
    }
    
    public function getWormChoices() {
        return array(
            'male' => 'Male',
            'female' => 'Female',
            'hermaphrodite' => 'Hermaphrodite',
        );
    }
    
    public function getFlyChoices() {
        return array(
            'male' => 'Male',
            'female' => 'Female',
        );
    }
    
    public function getYeastChoices() {
        return array(
            'mata' => 'MATa',
            'matalpha' => 'MATalpha',
            'diploid' => 'Diploid',
        );
    }
}
