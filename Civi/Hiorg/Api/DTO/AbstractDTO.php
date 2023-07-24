<?php

namespace Civi\Hiorg\Api\DTO;

class AbstractDTO {

  public function __construct(\stdClass $object) {
    $properties = (new \ReflectionClass($this))
      ->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
    foreach ($properties as $property) {
      $key = $property->getName();
      $type = $property->getType()->getName();
      switch (TRUE) {
        // Lookup property in top level of the given object.
        case property_exists($object, $key):
          $value = $object->$key;
          break;
        // Lookup property in "attributes" property of the given object.
        case property_exists($object, 'attributes') && property_exists($object->attributes, $key):
          $value = $object->attributes->$key;
          break;
      }
      if (isset($value)) {
        settype($value, $type);
        $this->$key = $value;
      }
    }
  }

}
