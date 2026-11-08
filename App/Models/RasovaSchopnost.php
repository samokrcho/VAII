<?php

namespace App\Models;

use Framework\Core\Model;

/**
 * Model tabuľky `rasova_schopnost` (názov tabuľky a kľúčov určuje App\Support\IdConventions).
 */
class RasovaSchopnost extends Model
{
    protected static ?array $fillable = [];

    public ?int $idRasovaSchopnost = null;
    public int $idRasa = 0;
    public string $nazov = '';
    public string $vlastnost = '';
    public ?string $citat = null;
    public ?string $text = null;

    public function getRasa(): ?Rasa
    {
        return $this->getOneRelated(Rasa::class);
    }
}
