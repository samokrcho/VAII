<?php

namespace App\Models;

use Framework\Core\Model;

/**
 * Model tabuľky `rasa` (názov tabuľky a kľúčov určuje App\Support\IdConventions).
 */
class Rasa extends Model
{
    protected static ?array $fillable = [];

    public ?int $idRasa = null;
    public string $nazov = '';
    public ?string $popis = null;

    /** @return RasovaSchopnost[] rasové schopnosti tejto rasy */
    public function getRasoveSchopnosti(): array
    {
        return $this->getAllRelated(RasovaSchopnost::class);
    }
}
