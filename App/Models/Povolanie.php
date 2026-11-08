<?php

namespace App\Models;

use Framework\Core\Model;

/**
 * Model tabuľky `povolanie` (názov tabuľky a kľúčov určuje App\Support\IdConventions).
 */
class Povolanie extends Model
{
    protected static ?array $fillable = [];

    public ?int $idPovolanie = null;
    public string $nazov = '';
    public ?string $popis = null;

    /** @return ZvlastnaSchopnost[] zvláštne schopnosti tohto povolania */
    public function getZvlastneSchopnosti(): array
    {
        return $this->getAllRelated(ZvlastnaSchopnost::class);
    }
}
