<?php

/**
 * Description of Search
 *
 * @author brady
 */
class Application_Form_SearchForm extends Zend_Form
{
    public function init()
    {
        $this->_initObservationElements();
        $this->_initLifespanElements();
        $this->_initGeneElements();
        $this->_initCompoundElements();
        $this->_initEnvironmentElements();

        $this->addElement('submit', 'Submit', array(
            'ignore'   => true,
            'label'    => 'Submit',
            'style'     => 'width:10em'
        ));

        // set decorator to view script
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'forms/advancedSearch.phtml'))
        ));
    }

    private function _initObservationElements()
    {
        $this->addElement('text', 'search', array(
            'label'       => 'Search Terms:',
            'required'    => false,
            'style' => 'width: 25em',
        ));

        $this->addElement('text', 'species', array(
            'label'       => 'Species:',
            'required'    => false,
            'style' => 'width: 18em',
        ));

        $this->addElement('text', 'strain', array(
            'label'       => 'Strain:',
            'required'    => false,
            'style' => 'width: 10em',
        ));

        $this->addElement('text', 'cellType', array(
            'label'       => 'Cell Type:',
            'required'    => false,
            'style' => 'width: 10em',
        ));

        $this->addElement('text', 'matingType', array(
            'label'       => 'Mating Type:',
            'required'    => false,
            'style' => 'width: 10em',
        ));

        $this->addElement('text', 'citationPubmedId', array(
            'label'      => 'PubMed ID:',
            'required'   => false,
            'style'      => 'width: 10em',
            'validators' => array(
                array('validator' => 'Int'),
            ),
        ));
    }

    private function _initLifespanElements()
    {
        $choices = array_merge(
            array('' => ''),
            Observation::getLifespanEffects()
        );
        $this->addElement('select', 'lifespanEffect', array(
            'label'       => 'Lifespan Effect:',
            'required'    => false,
            'multiOptions' => $choices,
        ));

        $choices = array_merge(
            array('' => ''),
            Observation::getLifespanMeasures()
        );
        $this->addElement('select', 'lifespanMeasure', array(
            'label'       => 'Lifespan Measure:',
            'required'    => false,
            'multiOptions' => $choices,
        ));

        $choices = array_merge(
            array('' => ''),
            Observation::getLifespanUnits()
        );
        $this->addElement('select', 'lifespanUnit', array(
            'label'       => 'Lifespan Units:',
            'required'    => false,
            'multiOptions' => $choices,
        ));

        $choices = array(
            '>' => '>',
            '<' => '<',
        );
        $this->addElement('select', 'lifespanOp', array(
            'required'    => false,
            'multiOptions' => $choices,
        ));
        $this->addElement('select', 'lifespanBaseOp', array(
            'required'    => false,
            'multiOptions' => $choices,
        ));
        $this->addElement('select', 'lifespanChangeOp', array(
            'required'    => false,
            'multiOptions' => $choices,
        ));

        $this->addElement('text', 'lifespan', array(
            'label'       => 'Lifespan',
            'required'    => false,
        ));

        $this->addElement('text', 'lifespanBase', array(
            'label'       => 'Lifespan Base',
            'required'    => false,
        ));
        $this->addElement('text', 'lifespanChange', array(
            'label'       => 'Lifespan Change',
            'required'    => false,
        ));
    }

    private function _initGeneElements()
    {
        $this->addElement('text', 'geneSymbol', array(
            'label'       => 'Symbol:',
            'required'    => false,
            'style' => 'width: 10em',
        ));

        $choices = array_merge(
            array('' => ''),
            ObservationGene::getAlleleTypes()
        );
        $this->addElement('select', 'geneAlleleType', array(
            'label'       => 'Allele Type:',
            'required'    => false,
            'multiOptions' => $choices
        ));
    }

    private function _initCompoundElements()
    {
        $this->addElement('text', 'compoundName', array(
            'label'       => 'Name:',
            'required'    => false,
            'style' => 'width: 10em',
        ));
    }

    private function _initEnvironmentElements()
    {
        $choices = array('' => '');
        $types = ObservationEnvironmentTable::getTypes();
        foreach ($types as $type) {
            $choices[$type] = $type;
        }
        $this->addElement('select', 'environmentType', array(
            'label'       => 'Type:',
            'required'    => false,
            'multiOptions' => $choices
        ));
        
        $this->addElement('text', 'environmentDescription', array(
            'label'       => 'Description:',
            'required'    => false,
            'style' => 'width: 25em',
        ));
    }

}

