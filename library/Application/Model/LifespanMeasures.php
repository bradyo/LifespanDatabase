<?php

class Application_Model_LifespanMeasures 
{
    public function getChoices() {
        return array(
            'mean' => 'Mean',
            'median' => 'Median',
            'max' => 'Max',
            'individual' => 'Individual',
        );
    }
}
