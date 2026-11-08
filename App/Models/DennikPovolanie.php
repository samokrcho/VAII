<?php

namespace App\Models;

use Framework\Core\Model;

/**
 * Model tabuľky `dennik_povolanie` (názov tabuľky a kľúčov určuje App\Support\IdConventions).
 */
class DennikPovolanie extends Model
{
    protected static ?array $fillable = ['uroven'];

    public ?int $idDennikPovolanie = null;
    public int $idDennik = 0;
    public int $idPovolanie = 0;
    public int $uroven = 0;

    public function getPovolanie(): ?Povolanie
    {
        return $this->getOneRelated(Povolanie::class);
    }
}
