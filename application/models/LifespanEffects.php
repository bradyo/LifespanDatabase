<?php

class Application_Model_LifespanEffects 
{
    public function getChoices() {
        return array(
            'none'       => 'None',
            'increased'  => 'Increased',
            'decreased'  => 'Decreased',
        );
    }
}
