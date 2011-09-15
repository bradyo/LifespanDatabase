<?php

class Application_Model_AlleleTypes {
    public function getChoices() {
        return array(
            'normal' => 'normal',
            'over-expression' => 'over-expression',
            'deletion / null' => 'deletion / null',
            'non-null recessive' => 'non-null recessive',
            'non-null dominant' => 'non-null dominant',
            'non-null semi-dominant' => 'non-null semi-dominant',
            'RNAi knockdown' => 'RNAi knockdown',
            'anti-sense RNA' => 'anti-sense RNA',
            'loss of function' => 'loss of function',
            'gain of function' => 'gain of function',
            'dominant negative' => 'dominant negative',
            'heterozygous diploid' => 'heterozygous diploid',
            'unknown' => 'unknown',
        );
    }
}
