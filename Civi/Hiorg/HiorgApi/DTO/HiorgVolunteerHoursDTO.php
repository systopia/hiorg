<?php
/*-------------------------------------------------------+
| SYSTOPIA HiOrg-Server API                              |
| Copyright (C) 2024 SYSTOPIA                            |
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

class HiorgVolunteerHoursDTO extends AbstractDTO {

  public string $id;

  public bool $freigegeben;

  public string $von;

  public string $bis;

  public float $stunden;

  public ?float $anfahrt_km;

  public ?float $freies_zahlenfeld;

  public ?string $anlass_id;

  public ?string $anlass_typ;

  public ?string $anlass_beschreibung;

  public ?string $typ_id;

  public ?string $user_id;

  public static function create(\stdClass $object): HiorgVolunteerHoursDTO {
    return new self($object);
  }

  public static function getPropertyValue(string $key, \stdClass $object) {
    switch ($key) {
      case 'anlass_id':
        $value = $object->relationships->anlass->data->id;
        break;
      case 'anlass_typ':
        $value = $object->relationships->anlass->data->attributes->typ;
        break;
      case 'anlass_beschreibung':
        $value = $object->relationships->anlass->data->attributes->beschreibung;
        break;
      case 'typ_id':
        $value = $object->relationships->typ->data->id;
        break;
      case 'user_id':
        $value = $object->relationships->user->data->id;
        break;
      default:
        $value = parent::getPropertyValue($key, $object);
    }
    return $value;
  }

}
