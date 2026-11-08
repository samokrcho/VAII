<?php

namespace App\Controllers;

use App\Models\Rasa;
use App\Models\RasovaSchopnost;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

/**
 * Class RasaController
 *
 * Informačná stránka s rasami a ich rasovými schopnosťami (len čítanie - R z CRUD).
 * Rasy a rasové schopnosti sú číselník naplnený z pravidiel hry.
 *
 * @package App\Controllers
 */
class RasaController extends BaseController
{
    public function authorize(Request $request, string $action): bool
    {
        return true;
    }

    /**
     * Zobrazí všetky rasy aj s ich rasovými schopnosťami.
     */
    public function index(Request $request): Response
    {
        $schopnosti = [];
        foreach (RasovaSchopnost::getAll(orderBy: 'id_rasova_schopnost') as $s) {
            $schopnosti[$s->idRasa][] = $s;
        }
        return $this->html(['rasy' => Rasa::getAll(orderBy: 'id_rasa'), 'schopnosti' => $schopnosti]);
    }
}
