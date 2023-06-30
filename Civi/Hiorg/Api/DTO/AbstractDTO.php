<?php

namespace Civi\Hiorg\Api\DTO;

class AbstractDTO {

  public function __construct(\stdClass $object) {
    $properties = (new \ReflectionClass($this))
      ->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
    foreach (array_map(function(\ReflectionProperty $property) { return $property->getName(); }, $properties) as $key) {
      switch (TRUE) {
        // Lookup property in top level of the given object.
        case property_exists($object, $key):
          $this->$key = $object->key;
          break;
        // Lookup property in "attributes" property of the given object.
        case property_exists($object, 'attributes') && property_exists($object->attributes, $key):
          $this->$key = $object->attributes->$key;
          break;
      }
    }
  }

}
