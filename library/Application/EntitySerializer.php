<?php

use Doctrine\ORM\Mapping\ClassMetadata,
    Doctrine\Common\Util\Inflector,
    Doctrine\ORM\EntityManager;

class Application_EntitySerializer
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $_em;

    public function __construct($em)
    {
        $this->setEntityManager($em);
    }

    /**
     *
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->_em;
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->_em = $em;

        return $this;
    }

    protected function _serializeEntity($entity)
    {
        $className = get_class($entity);
        $metadata = $this->_em->getClassMetadata($className);

        $data = array();

        foreach ($metadata->fieldMappings as $field => $mapping) {
            $value = $metadata->reflFields[$field]->getValue($entity);
            $field = Inflector::tableize($field);
            if ($value instanceof \DateTime) {
                // We cast DateTime to array to keep consistency with array result
                $data[$field] = (array)$value;
            } elseif (is_object($value)) {
                $data[$field] = (string)$value;
            } else {
                $data[$field] = $value;
            }
        }

        foreach ($metadata->associationMappings as $field => $mapping) {
            $key = Inflector::tableize($field);
            if ($mapping['isCascadeDetach']) {
                $data[$key] = $this->_serializeEntity(
                    $metadata->reflFields[$field]
                        ->getValue($entity)
                );
            } elseif ($mapping['isOwningSide'] && $mapping['type'] & ClassMetadata::TO_ONE) {
                if (null !== $metadata->reflFields[$field]->getValue($entity)) {
                    $data[$key] = $this->getEntityManager()
                        ->getUnitOfWork()
                        ->getEntityIdentifier(
                            $metadata->reflFields[$field]
                                ->getValue($entity)
                            );
                } else {
                    // In some case the relationship may not exist, but we want
                    // to know about it
                    $data[$key] = null;
                }
            }
        }

        return $data;
    }

    /**
    * Serialize an entity to an array
    *
    * @param The entity $entity
    * @return array
    */
    public function toArray($entity)
    {
        return $this->_serializeEntity($entity);
    }

    /**
    * Convert an entity to a JSON object
    *
    * @param The entity $entity
    * @return string
    */
    public function toJson($entity)
    {
        return json_encode($this->toArray($entity));
    }
    
        
    public function toDOMDocument($entity)
    {
        $arrToXml = function($node, $data) use (&$arrToXml) {
            foreach ($data AS $k => $v) {
                $child = $node->ownerDocument->createElement($k);
                $node->appendChild($child);
                if (is_array($v)) {
                    $arrToXml($child, $v);
                } else {
                    $child->appendChild($node->ownerDocument->createTextNode($v));
                }
            }
        };
        
        $className = get_class($entity);
        $class = $this->_em->getClassMetadata($className);
        
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $root = $dom->createElement(Inflector::tableize($class->reflClass->getShortName()));
        $dom->appendChild($root);
        
        $arrToXml($root, $this->toArray($entity));
        
        return $dom;
    }
    
    public function toXml($entity, $formatOutput = false)
    {
        $dom = $this->toDOMDocument($entity);
        $dom->formatOutput = $formatOutput;
        return $dom->saveXML();
    }
}
