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

namespace Civi\Hiorg\HiorgApi\DTO;

class HiorgVerificationDTO extends AbstractDTO {

  public string $id;

  public string $bezeichnung;

  public bool $pruefergebnis_bestanden;

  public ?string $pruefergebnis_einschraenkungen;

  public ?string $letzte_pruefung;

  public ?string $naechste_pruefung;

  public static function create(\stdClass $object): HiorgVerificationDTO {
    return new self($object);
  }

  public static function getPropertyValue(string $key, \stdClass $object) {
    switch ($key) {
      case 'pruefergebnis_bestanden':
      case 'pruefergebnis_einschraenkungen':
        $realKey = substr($key, strlen('pruefergebnis_'));
        $value = $object->attributes->pruefergebnis->$realKey;
        break;
      case 'letzte_pruefung':
        // The actual attribute has a typo ("letze" instead of "letzte").
        $value = $object->attributes->letze_pruefung;
        break;
      default:
        $value = parent::getPropertyValue($key, $object);
    }
    return $value;
  }

}
