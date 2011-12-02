<?php

namespace Application\Model;

class MatingTypes 
{
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
            '' => null,
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
            '' => null,
            'male' => 'Male',
            'female' => 'Female',
        );
    }
    
    public function getWormChoices() {
        return array(
            '' => null,
            'male' => 'Male',
            'female' => 'Female',
            'hermaphrodite' => 'Hermaphrodite',
        );
    }
    
    public function getFlyChoices() {
        return array(
            '' => null,
            'male' => 'Male',
            'female' => 'Female',
        );
    }
    
    public function getYeastChoices() {
        return array(
            '' => null,
            'mata' => 'MATa',
            'matalpha' => 'MATalpha',
            'diploid' => 'Diploid',
        );
    }
}
