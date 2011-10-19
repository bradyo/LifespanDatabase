<?php

class Application_Form_ObservationForm extends Zend_Form
{
    /**
     * The user editing the form.
     * @var Application_Model_User
     */
    private $user;
    
    /**
     * @var Application_Model_LifespanEffects
     */
    private $lifespanEffects;
    
    /**
     * @var Application_Model_LifespanMeasures
     */
    private $lifespanMeasures;
    
    /**
     * @var Application_Model_LifespanUnits
     */
    private $lifespanUnits;
    
    /**
     * @var Application_Model_MatingTypes
     */
    private $matingTypes;
    
    
    /**
     * @param Application_Model_User $user
     * @param array $options 
     */
    public function __construct($user, $options = null) {
        parent::__construct($options);
        $this->user = $user;
        
        $this->lifespanEffects = new Application_Model_LifespanEffects();
        $this->lifespanMeasures = new Application_Model_LifespanMeasures();
        $this->lifespanUnits = new Application_Model_LifespanUnits();
        $this->matingTypes = new Application_Model_MatingTypes();
    }

    public function init() {
        $this->addBaseElements();
        $this->addLifespanElements();
        $this->addCitationElements();
        $this->addInterventionSubForms();
        
        if ($this->user->isModerator()) {
            $this->addReviewerFields();
        }
        
        $this->addElement('submit', 'Submit', array(
            'ignore'   => true,
            'label'    => 'Submit',
        ));
    }
    
    private function addInterventionSubForms() {
        $this->addSubForm(new Zend_Form_SubForm(), 'genes');
        $this->addSubForm(new Zend_Form_SubForm(), 'compounds');
        $this->addSubForm(new Zend_Form_SubForm(), 'environments');
    }

    private function getStatusChoices() {
        return array(
            Application_Model_Observation::STATUS_PUBLIC,
            Application_Model_Observation::STATUS_DELETED,
        );
    }
    
    private function addBaseElements() {
        $this->addElement('hidden', 'id', array(
           'required' => false,
        ));
        
        $this->addElement('select', 'status', array(
            'label' => 'Set Status:',
            'multiOptions' => $this->getStatusChoices(),
            'required' => true,
            'decorators' => array(
                'Label', 'ViewHelper'
            )
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
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));

        $this->addElement('text', 'strain', array(
            'label' => 'Strain:',
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));

        $this->addElement('text', 'strainGenotype', array(
            'label' => 'Genotype:',
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));

        $this->addElement('text', 'cellType', array(
            'label' => 'Cell Type:',
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));

        $this->addElement('select', 'matingType', array(
            'label' => 'Mating Type:',
            'multiOptions' => $this->matingTypes->getOptions(),
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));

        $this->addElement('textarea', 'description', array(
            'label' => 'Description:',
            'required' => true,
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

    private function addLifespanElements() {
        $this->addElement('text', 'lifespan', array(
            'label' => 'Lifespan:',
            'required' => false,
            'validators' => array(
                array('validator' => 'Float'),
            ),
            'size' => 8,
            'decorators' => array('Label', 'ViewHelper', 'Errors')
        ));

        $this->addElement('text', 'lifespanBase', array(
            'label' => 'Lifespan (WT):',
            'required' => false,
            'validators' => array(
                array('validator' => 'Float'),
            ),
            'size' => 8,
            'decorators' => array('Label', 'ViewHelper', 'Errors')
        ));

        $this->addElement('radio', 'lifespanUnit', array(
            'label'         => 'Units:',
            'required'      => false,
            'multiOptions'  => $this->lifespanUnits->getChoices(),
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

        $this->addElement('radio', 'lifespanEffect', array(
            'label'         => 'Effect:',
            'required'      => false,
            'multiOptions'  => $this->lifespanEffects->getChoices(),
            'value'         => '',
            'class'         => 'radio'
        ));

        $this->addElement('radio', 'lifespanMeasure', array(
            'label' => 'Measure:',
            'required' => false,
            'multiOptions' => $this->lifespanMeasures->getChoices(),
            'value' => '',
            'class' => 'radio'
        ));
    }

    private function addCitationElements() {
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
    
    public function addReviewElements() {
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
    
    public function populate(array $values) {
        parent::populate($values);
        
        // populate sub forms
        if (isset($values['genes'])) {
            $genesForm = $this->getSubForm('genes');
            foreach ($values['genes'] as $i => $geneValues) {
                $geneForm = new Application_Form_ObservationFormGeneForm();
                $geneForm->populate($geneValues);
                $genesForm->addSubForm($geneForm, (string)$i);
            }
        }
        if (isset($values['compounds'])) {
            $compoundsForm = $this->getSubForm('compounds');
            foreach ($values['compounds'] as $i => $compoundValues) {
                $compoundForm = new Application_Form_ObservationFormCompound();
                $compoundForm->populate($compoundValues);
                $compoundsForm->addSubForm($compoundForm, (string)$i);
            }
        }
        if (isset($values['environments'])) {
            $environmentsForm = $this->getSubForm('environments');
            foreach ($values['environments'] as $i => $environmentValues) {
                $environmentForm = new Application_Form_ObservationFormEnvironment();
                $environmentForm->populate($environmentValues);
                $environmentsForm->addSubForm($environmentForm, (string)$i);
            }
        }
    }
}
