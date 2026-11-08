<?php

namespace App\Models;

use Framework\Core\Model;

/**
 * Model tabuľky `dennik_zvlastna_schopnost` (názov tabuľky a kľúčov určuje App\Support\IdConventions).
 */
class DennikZvlastnaSchopnost extends Model
{
    protected static ?array $fillable = [];

    public ?int $idDennikZvlastnaSchopnost = null;
    public int $idDennik = 0;
    public int $idPovolanie = 0;
    public int $idSchopnost = 0;
    public int $urovenZiskania = 0;

    public function getZvlastnaSchopnost(): ?ZvlastnaSchopnost
    {
        // kľúč ZvlastnaSchopnost je id_schopnost, nie id_zvlastna_schopnost
        return $this->getOneRelated(ZvlastnaSchopnost::class, 'id_schopnost');
    }
}
