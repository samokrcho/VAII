<?php

namespace App\Models;

use Framework\Core\Model;

/**
 * Model tabuľky `zvlastna_schopnost` (názov tabuľky a kľúčov určuje App\Support\IdConventions).
 */
class ZvlastnaSchopnost extends Model
{
    protected static ?string $primaryKey = 'id_schopnost';
    protected static ?array $fillable = [];

    public ?int $idSchopnost = null;
    public int $idPovolanie = 0;
    public string $nazov = '';
    public string $vlastnost = '';
    public ?string $citat = null;
    public ?string $text = null;
    public int $minimalnaUroven = 0;

    public function getPovolanie(): ?Povolanie
    {
        return $this->getOneRelated(Povolanie::class);
    }
}
