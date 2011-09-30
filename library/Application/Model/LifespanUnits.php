<?php

class Application_Model_LifespanUnits 
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
