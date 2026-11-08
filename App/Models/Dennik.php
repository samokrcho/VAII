<?php

namespace App\Models;

use Framework\Core\Model;

/**
 * Model tabuľky `dennik` (názov tabuľky a kľúčov určuje App\Support\IdConventions).
 */
class Dennik extends Model
{
    protected static ?array $fillable = ['menoPostavy', 'popis', 'idRasa', 'idRasovaSchopnost', 'jeVerejny', 'telo', 'dusa', 'vplyv', 'ohrozenie', 'vyhoda', 'dukaty', 'grose', 'haliere', 'vybavenie'];

    public ?int $idDennik = null;
    public int $idPouzivatel = 0;
    public int $idRasa = 0;
    public int $idRasovaSchopnost = 0;
    public string $menoPostavy = '';
    public ?string $popis = null;
    public int $jeVerejny = 0;
    public int $telo = 0;
    public int $dusa = 0;
    public int $vplyv = 0;
    public int $ohrozenie = 0;
    public int $vyhoda = 0;
    public int $dukaty = 0;
    public int $grose = 0;
    public int $haliere = 0;
    public ?string $vybavenie = null;
    public ?string $vytvoreny = null;
    public ?string $upraveny = null;

    public function getPouzivatel(): ?Pouzivatel
    {
        return $this->getOneRelated(Pouzivatel::class);
    }

    public function getRasa(): ?Rasa
    {
        return $this->getOneRelated(Rasa::class);
    }

    public function getRasovaSchopnost(): ?RasovaSchopnost
    {
        return $this->getOneRelated(RasovaSchopnost::class);
    }

    /** @return DennikPovolanie[] povolania postavy s úrovňou */
    public function getPovolania(): array
    {
        return $this->getAllRelated(DennikPovolanie::class);
    }

    /** @return DennikZvlastnaSchopnost[] vybrané zvláštne schopnosti postavy */
    public function getZvlastneSchopnosti(): array
    {
        return $this->getAllRelated(DennikZvlastnaSchopnost::class);
    }
}
