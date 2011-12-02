<?php

namespace Application\Model;

class LifespanUnits 
{
    public function getChoices() {
        return array(
            'days' => 'Days',
            'years' => 'Years',
            'divisions' => 'Divisions',
            'si' => 'SI',
        );
    }
}
