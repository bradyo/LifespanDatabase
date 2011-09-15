<?php

/**
 * @Entity
 * @Table(name="observation")
 */
class Application_Model_ObservationLifespan {
    
    /**
     * @var double Lifespan value with interventions.
     * @Column(name="lifespan", type="double")
     */
    private $value;
    
    /**
     * @var double Lifespan value without interventions (experiment control).
     * @Column(name="lifespan_base", type="double")
     */
    private $baseValue;
    
    /**
     * @var string Units for lifespan values, i.e. days, divisions, etc...)
     * @Column(name="lifespan_units", type="string")
     */
    private $units;
    
    /**
     * @var double Lifespan percent change of intervention vs control.
     * @Column(name="lifespan_change", type="double")
     */
    private $percentChange;
    
    /**
     * @var string Direction of lifespan change, if significant.
     * @Column(name="lifespan_effect", type="string")
     */
    private $effect;
    
    /**
     * @var string Type of lifespan measurement, i.e. mean, median, max, etc...
     * @Column(name="lifespan_measure", type="string")
     */
    private $measure;
    
    
    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function getBaseValue() {
        return $this->baseValue;
    }

    public function setBaseValue($baseValue) {
        $this->baseValue = $baseValue;
    }

    public function getUnits() {
        return $this->units;
    }

    public function setUnits($units) {
        $this->units = $units;
    }

    public function getPercentChange() {
        return $this->percentChange;
    }

    public function setPercentChange($percentChange) {
        $this->percentChange = $percentChange;
    }

    public function getEffect() {
        return $this->effect;
    }

    public function setEffect($effect) {
        $this->effect = $effect;
    }

    public function getMeasure() {
        return $this->measure;
    }

    public function setMeasure($measure) {
        $this->measure = $measure;
    }
}
