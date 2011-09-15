<?php

class Application_Model_LifespanUnits 
{
    public function getOptions() {
        return array(
            'days' => 'Days',
            'years' => 'Years',
            'divisions' => 'Divisions',
            'si' => 'SI',
        );
    }
}
