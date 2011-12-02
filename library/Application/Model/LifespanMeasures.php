<?php

namespace Application\Model;

class LifespanMeasures 
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
