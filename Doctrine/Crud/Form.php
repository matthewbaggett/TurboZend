<?php 
class Turbo_Doctrine_Crud_Form extends \EasyBib_Form{
	
	protected $mapOfNames = array(
			'_id' => 'ID',
	);
	protected $ignored_columns = array(
			'_id',
			'_em'
	);
	protected $mapOfCustomisation = array();
	
	public function init($nameOfEntity){
		
		$em = Zend_Registry::get('em');
		
		$entityReflection = new ReflectionClass($nameOfEntity);
		
		$entityName = ltrim(str_replace($entityReflection->getNamespaceName(),"",$entityReflection->getName()),"\\");
		
		parent::init();
		
        $this->setMethod('POST');
        $this->setAction($this->getView()->baseUrl('/index/add-custom'));
        $this->setAttrib('id', "add{$entityName}");
        foreach($entityReflection->getProperties() as $property){
        	if(in_array($property->name,$this->ignored_columns)){
        		continue;
        	}
        	
        	if(isset($this->mapOfCustomisation[$property->name])){
        		$fields[$property->name] = new $this->mapOfCustomisation[$property->name]['type']($property->name);
        		switch($this->mapOfCustomisation[$property->name]['type']){
        			case '\Zend_Form_Element_Select':
        				$mappedEntity = $this->mapOfCustomisation[$property->name]['options']['table'];
        				$mappedParameter = $this->mapOfCustomisation[$property->name]['options']['parameter'];
        				$mappedEntities = $em->getRepository($mappedEntity)->findAll();
        				foreach($mappedEntities as $entityItem){
        					$fields[$property->name]->addMultiOption($entityItem->getId(),$entityItem->$mappedParameter());
        				}
        				break;

        			case '\Zend_Form_Element_Hidden':
        				$fields[$property->name]->setValue($this->mapOfCustomisation[$property->name]['value']);
        				break;
        			
        			default:
        				//nada.
        		}
        	}else{
        		$fields[$property->name] = new \Zend_Form_Element_Text($property->name);
           	}
        	
        	$fields[$property->name]->setRequired(true);
        	
        	$mapped_name = $property->name;
        	if(isset($this->mapOfNames[$mapped_name])){
        		$mapped_name = $this->mapOfNames[$mapped_name];
        	}
        	
        	$fields[$property->name]->setLabel($mapped_name);
        	
        	$this->addElement($fields[$property->name]);
        }

        $submit = new \Zend_Form_Element_Button('submit');
        $submit->setLabel("Add new {$entityName}");
        
        $this->addElement($submit);

        \EasyBib_Form_Decorator::setFormDecorator(
            $this, \EasyBib_Form_Decorator::BOOTSTRAP, 'submit'
        );
	}
}