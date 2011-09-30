<?php

/**
 * Description of Observation
 *
 * @author brady
 */
class Application_Form_Observation extends Zend_Form
{
    private $_canPublish;

    public function __construct($canPublish = null)
    {
        $this->_canPublish = ($canPublish === true) ? true : false;
        parent::__construct();
    }

    public function canPublish()
    {
        return $this->_canPublish;
    }

    public function init()
    {
        $this->setMethod(Zend_Form::METHOD_POST);
        $this->setAction('');

        $this->initBaseElements();
        $this->initLifespanElements();
        $this->initCitationElements();

        $this->addSubForm(new Zend_Form_SubForm(), 'genes');
        $this->addSubForm(new Zend_Form_SubForm(), 'compounds');
        $this->addSubForm(new Zend_Form_SubForm(), 'environments');

        if ($this->_canPublish === true) {
            $this->initPublishElements();
        }

        $this->addElement('submit', 'Submit', array(
            'ignore'   => true,
            'label'    => 'Submit',
            'style'     => 'width:10em'
        ));

        // set decorator to view script
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'forms/observation.phtml'))
        ));
    }

    private function initBaseElements()
    {
        $this->addElement('hidden', 'id', array(
           'required' => false,
        ));

        $this->addElement('text', 'ncbiTaxId', array(
            'label' => 'NCBI Species Id:',
            'validators' => array(
                array('validator' => 'Int'),
            ),
            'size' => 6,
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));
        
        $this->addElement('text', 'species', array(
            'label' => 'Species:',
            'validators' => array(
            ),
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));

        $this->addElement('text', 'strain', array(
            'label' => 'Strain:',
            'validators' => array(
            ),
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));

        $this->addElement('text', 'strainGenotype', array(
            'label' => 'Genotype:',
            'validators' => array(
            ),
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));

        $this->addElement('text', 'cellType', array(
            'label' => 'Cell Type:',
            'validators' => array(
            ),
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));

        $matingTypeOptions = array_merge(
            array('' => ''),
            Observation::getMatingTypes()
        );
        $this->addElement('select', 'matingType', array(
            'label' => 'Mating Type:',
            'multiOptions' => $matingTypeOptions,
            'validators' => array(
            ),
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));

        $this->addElement('textarea', 'body', array(
            'label'      => 'Observation Details:',
            'required'   => true,
            'rows' => 6,
            'cols' => 80,
        ));

        $this->addElement('text', 'temperature', array(
            'label' => 'Experiment Temperature:',
            'validators' => array(
                array('validator' => 'Float'),
            ),
            'size' => 6,
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));
    }

    private function initLifespanElements()
    {
        $this->addElement('text', 'lifespan', array(
            'label'         => 'Lifespan:',
            'required'      => false,
            'validators'    => array(
                array('validator' => 'Float'),
            ),
            'size' => 8,
            'decorators' => array('Label', 'ViewHelper', 'Errors')
        ));

        $this->addElement('text', 'lifespanBase', array(
            'label'         => 'Lifespan (WT):',
            'required'      => false,
            'validators'    => array(
                array('validator' => 'Float'),
            ),
            'size' => 8,
            'decorators' => array('Label', 'ViewHelper', 'Errors')
        ));

        $unitChoices = array(
            '' => 'N/A',
            'days' => 'Days',
            'years' => 'Years',
            'divisions' => 'Divisions',
            'si' => 'SI'
        );
        $this->addElement('radio', 'lifespanUnit', array(
            'label'         => 'Units:',
            'required'      => false,
            'multiOptions'  => $unitChoices,
            'class' => 'radio',
            'value' => '',
            'decorators' => array('Label', 'ViewHelper', 'Errors')
        ));

        $this->addElement('text', 'lifespanChange', array(
            'label'         => 'Magnitude:',
            'required'      => false,
            'validators'    => array(
                array('validator' => 'Float'),
            ),
            'size' => 5,
            'decorators' => array('Label', 'ViewHelper', 'Errors')
        ));


        $effectChoices = array_merge(
            array('' => 'N/A'),
            Observation::getLifespanEffects()
        );
        $this->addElement('radio', 'lifespanEffect', array(
            'label'         => 'Effect:',
            'required'      => false,
            'multiOptions'  => $effectChoices,
            'value'         => '',
            'class'         => 'radio'
        ));

        $measureChoices = array_merge(
            array('' => 'N/A'),
            Observation::getLifespanMeasures()
        );
        $this->addElement('radio', 'lifespanMeasure', array(
            'label'         => 'Measure:',
            'required'      => false,
            'multiOptions'  => $measureChoices,
            'value'         => '',
            'class'         => 'radio'
        ));
    }

    private function initCitationElements()
    {
        $this->addElement('text', 'citationPubmedId', array(
            'label' => 'PubMed Id:',
            'validators' => array(
                array('validator' => 'Int'),
            ),
            'size' => 8,
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));
        $this->addElement('text', 'citationYear', array(
            'label' => 'Year:',
            'validators' => array(
                array('validator' => 'Int'),
            ),
            'size' => 4,
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));
        $this->addElement('text', 'citationAuthor', array(
            'label' => 'Author:',
            'required' => true,
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 255)),
            ),
            'size' => 60,
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));
        $this->addElement('text', 'citationTitle', array(
            'label' => 'Title:',
            'required' => true,
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 255)),
            ),
            'size' => 60,
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));
        $this->addElement('text', 'citationSource', array(
            'label' => 'Source:',
            'required' => true,
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 255)),
            ),
            'size' => 60,
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));
    }

    private function initPublishElements()
    {
        $statusChocies = Observation::getStatusChoices();
        $this->addElement('select', 'status', array(
            'label'         => 'Status:',
            'required'      => false,
            'value'         => 'accepted',
            'multiOptions'  => $statusChocies,
        ));

        $this->addElement('textarea', 'reviewerComment', array(
            'label' => 'Comment:',
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
            'rows' => 4,
            'cols' => 80,
        ));
    }

    public function setValues($values)
    {
        // popluate parent form
        $this->populate($values);

        // populate sub forms
        if (isset($values['genes'])) {
            $genesForm = $this->getSubForm('genes');
            foreach ($values['genes'] as $i => $geneValues) {
                $geneForm = new Application_Form_ObservationGene();
                $geneForm->populate($geneValues);
                $genesForm->addSubForm($geneForm, (string)$i);
            }
        }
        if (isset($values['compounds'])) {
            $compoundsForm = $this->getSubForm('compounds');
            foreach ($values['compounds'] as $i => $compoundValues) {
                $compoundForm = new Application_Form_ObservationCompound();
                $compoundForm->populate($compoundValues);
                $compoundsForm->addSubForm($compoundForm, (string)$i);
            }
        }
        if (isset($values['environments'])) {
            $environmentsForm = $this->getSubForm('environments');
            foreach ($values['environments'] as $i => $environmentValues) {
                $environmentForm = new Application_Form_ObservationEnvironment();
                $environmentForm->populate($environmentValues);
                $environmentsForm->addSubForm($environmentForm, (string)$i);
            }
        }
    }

    public function isValid($data)
    {
        $this->setValues($data);
        return parent::isValid($data);
    }

}

