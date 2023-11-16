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

class HiorgUserDTO extends AbstractDTO {

  public static function create(\stdClass $object): HiorgUserDTO {
    return new self($object);
  }

  public string $id;

  public string $vorname;

  public string $nachname;

  public string $anrede;

  public string $username;

  public string $telpriv;

  public string $teldienst;

  public string $handy;

  public string $email;

  public string $adresse;

  public string $plz;

  public string $ort;

  public string $land;

  public string $gebdat;

  public string $gebort;

  public array $gruppen_namen;

  public string $orgakuerzel;

  public bool $leitung;

  public bool $gesperrt;

  public array $rechte;

  public array $qualifikationen;

  public array $relationships;

  public array $fahrerlaubnis;

  public string $angehoerige;

  public string $kontoinhaber;

  public string $iban;

  public string $bic;

  public string $kreditinstitut;

  public string $mitgliednr;

  public string $mitglied_seit;

  public string $austritt_datum;

  public string $wechseljgddat;

  public string $beruf;

  public string $arbeitgeber;

  public string $bemerkung;

  public string $funktion;

  public array $benutzerdefinierte_felder;

}
