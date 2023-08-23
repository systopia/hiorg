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

class HiorgUserDTO extends AbstractDTO {

  public string $id;

  public string $vorname;

  public string $nachname;

  public string $telpriv;

  public string $teldienst;

  public string $handy;

  public string $email;

  public string $adresse;

  public string $plz;

  public string $ort;

  public string $land;

  public string $gebdat;

  public array $gruppen_namen;

  public string $orgakuerzel;

  public bool $leitung;

  public bool $gesperrt;

  public array $rechte;

  public array $qualifikationen;

  public array $relationships;

}
