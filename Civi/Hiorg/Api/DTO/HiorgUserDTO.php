<?php

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
