<?php

namespace App\Controllers;

use App\Models\Povolanie;
use App\Models\ZvlastnaSchopnost;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

/**
 * Class PovolanieController
 *
 * Informačná stránka s povolaniami a ich zvláštnymi schopnosťami (len čítanie - R z CRUD).
 *
 * @package App\Controllers
 */
class PovolanieController extends BaseController
{
    public function authorize(Request $request, string $action): bool
    {
        return true;
    }

    /**
     * Zobrazí všetky povolania aj s ich zvláštnymi schopnosťami.
     */
    public function index(Request $request): Response
    {
        $schopnosti = [];
        foreach (ZvlastnaSchopnost::getAll(orderBy: 'id_schopnost') as $s) {
            $schopnosti[$s->idPovolanie][] = $s;
        }
        return $this->html(['povolania' => Povolanie::getAll(orderBy: 'id_povolanie'), 'schopnosti' => $schopnosti]);
    }
}
