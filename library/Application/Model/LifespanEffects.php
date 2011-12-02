<?php

namespace Application\Model;

class LifespanEffects 
{
    public function getChoices() {
        return array(
            'none' => 'None',
            'increased' => 'Increased',
            'decreased' => 'Decreased',
        );
    }
}
