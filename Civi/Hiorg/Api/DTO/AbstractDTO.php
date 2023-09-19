<?php
/*-------------------------------------------------------+
| SYSTOPIA HiOrg-Server API                              |
| Copyright (C) 2023 SYSTOPIA                            |
| Author: J. Schuppe (schuppe@systopia.de)               |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/

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
          if ($key === 'benutzerdefinierte_felder') {
            // Identify custom fields by "name", not "id".
            $value = array_column($object->attributes->benutzerdefinierte_felder, 'value', 'name');
          }
          else {
            $value = $object->attributes->$key;
          }
          break;
      }
      if (isset($value)) {
        settype($value, $type);
        $this->$key = $value;
      }
    }
  }

}
