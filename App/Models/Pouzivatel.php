<?php

namespace App\Models;

use Framework\Core\Model;

/**
 * Model tabuľky `pouzivatel` (názov tabuľky a kľúčov určuje App\Support\IdConventions).
 */
class Pouzivatel extends Model
{
    protected static ?array $fillable = ['meno', 'email'];

    public ?int $idPouzivatel = null;
    public string $meno = '';
    public string $email = '';
    public string $hesloHash = '';
    public string $rola = '';
    public ?string $vytvoreny = null;
}
