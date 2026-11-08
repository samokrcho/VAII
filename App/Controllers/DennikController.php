<?php

namespace App\Controllers;

use App\Models\Dennik;
use App\Models\DennikPovolanie;
use App\Models\Pouzivatel;
use App\Models\Povolanie;
use App\Models\RasovaSchopnost;
use App\Models\Rasa;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

/**
 * Class DennikController
 *
 * Spravuje denníky postáv hry Dračí doupě II:
 *  - index  - zoznam denníkov (dennik_list),
 *  - detail - konkrétny denník postavy (dennik),
 *  - create - formulár na vytvorenie nového denníka,
 *  - store  - uloženie nového denníka do databázy (POST).
 *
 * Zatiaľ pracuje len s placeholder dátami. Po pridaní modelu a databázy sa pole nižšie nahradí načítaním
 * z databázy (napr. Dennik::getAll()).
 *
 * @package App\Controllers
 */
class DennikController extends BaseController
{
    /**
     * Denníky si môže prezerať ktokoľvek (rola "guest").
     *
     * @param Request $request
     * @param string $action
     * @return bool
     */
    public function authorize(Request $request, string $action): bool
    {
        return true;
    }

    /**
     * Zobrazí zoznam denníkov.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->html(['denniky' => $this->getPlaceholderDenniky()]);
    }

    /**
     * Zobrazí denník jednej postavy (zatiaľ prázdny formulár podľa papierového denníka).
     *
     * @param Request $request
     * @return Response
     */
    public function detail(Request $request): Response
    {
        return $this->html();
    }

    /**
     * Zobrazí formulár na vytvorenie nového denníka.
     */
    public function create(Request $request): Response
    {
        return $this->html($this->formData());
    }

    /**
     * Spracuje formulár (POST): overí vstup, uloží denník (a prípadne jeho prvé povolanie) a presmeruje na detail.
     * Pri chybe znova zobrazí formulár s hláškami a vyplnenými hodnotami.
     */
    public function store(Request $request): Response
    {
        if (!$request->isPost()) {
            return $this->redirect($this->url('dennik.create'));
        }

        $meno = trim((string)$request->post('menoPostavy'));
        $idRasa = (int)$request->post('idRasa');
        $idRasovaSchopnost = (int)$request->post('idRasovaSchopnost');
        $idPovolanie = (int)$request->post('idPovolanie');
        $uroven = (int)$request->post('uroven');

        $errors = [];
        if ($meno === '' || mb_strlen($meno) > 120) {
            $errors['menoPostavy'] = 'Zadej jméno postavy (nejvýše 120 znaků).';
        }
        $rasa = $idRasa > 0 ? Rasa::getOne($idRasa) : null;
        if ($rasa === null) {
            $errors['idRasa'] = 'Vyber rasu.';
        }
        $rasovaSchopnost = $idRasovaSchopnost > 0 ? RasovaSchopnost::getOne($idRasovaSchopnost) : null;
        if ($rasovaSchopnost === null) {
            $errors['idRasovaSchopnost'] = 'Vyber rasovou schopnost.';
        } elseif ($rasa !== null && $rasovaSchopnost->idRasa !== (int)$rasa->idRasa) {
            // rasová schopnosť musí patriť k vybranej rase (kontroluje aj cudzí kľúč v DB)
            $errors['idRasovaSchopnost'] = 'Rasová schopnost neodpovídá vybrané rase.';
        }
        if ($idPovolanie > 0) {
            if (Povolanie::getOne($idPovolanie) === null) {
                $errors['idPovolanie'] = 'Neznámé povolání.';
            } elseif ($uroven < 1 || $uroven > 5) {
                $errors['uroven'] = 'Úroveň musí být 1 až 5.';
            }
        }

        if ($errors) {
            return $this->html($this->formData($errors, $request->post()), 'create');
        }

        $dennik = new Dennik();
        // TODO: po pridaní prihlasovania nastaviť prihláseného používateľa. Zatiaľ je vlastníkom prvý používateľ v DB.
        $dennik->idPouzivatel = (int)Pouzivatel::getAll(orderBy: 'id_pouzivatel', limit: 1)[0]->idPouzivatel;
        $dennik->menoPostavy = $meno;
        $dennik->idRasa = (int)$rasa->idRasa;
        $dennik->idRasovaSchopnost = (int)$rasovaSchopnost->idRasovaSchopnost;
        $dennik->jeVerejny = $request->post('jeVerejny') ? 1 : 0;
        $dennik->vytvoreny = $dennik->upraveny = date('Y-m-d H:i:s');
        $dennik->save();

        if ($idPovolanie > 0) {
            $dp = new DennikPovolanie();
            $dp->idDennik = (int)$dennik->idDennik;
            $dp->idPovolanie = $idPovolanie;
            $dp->uroven = $uroven;
            $dp->save();
        }

        return $this->redirect($this->url('dennik.detail', ['id' => $dennik->idDennik]));
    }

    /**
     * Dáta pre formulár nového denníka (číselníky + chyby + pôvodné hodnoty).
     *
     * @param array<string, string> $errors
     * @param array<string, mixed> $old
     * @return array<string, mixed>
     */
    private function formData(array $errors = [], array $old = []): array
    {
        return [
            'rasy' => Rasa::getAll(orderBy: 'id_rasa'),
            'rasoveSchopnosti' => RasovaSchopnost::getAll(orderBy: 'id_rasa, id_rasova_schopnost'),
            'povolania' => Povolanie::getAll(orderBy: 'id_povolanie'),
            'errors' => $errors,
            'old' => $old,
        ];
    }

    /**
     * Placeholder dáta do zoznamu denníkov.
     *
     * @return array<int, array<string, int|string>>
     */
    private function getPlaceholderDenniky(): array
    {
        $postavy = [
            ['Lorem Ipsum', 'Trpaslík', 'Bojovník', 5],
            ['Dolor Sit Amet', 'Elf', 'Zaříkavač', 3],
            ['Consectetur', 'Člověk', 'Kejklíř', 2],
            ['Adipiscing Elit', 'Hobit', 'Mastičkár', 4],
            ['Sed Do Eiusmod', 'Člověk', 'Bojovník', 1],
            ['Tempor Incididunt', 'Elf', 'Lovec', 6],
            ['Ut Labore', 'Hobit', 'Kejklíř', 2],
            ['Et Dolore Magna', 'Trpaslík', 'Mastičkár', 3],
            ['Aliqua Enim', 'Člověk', 'Zaříkavač', 4],
            ['Minim Veniam', 'Elf', 'Kejklíř', 1],
            ['Quis Nostrud', 'Trpaslík', 'Lovec', 2],
            ['Exercitation', 'Hobit', 'Bojovník', 3],
        ];

        $denniky = [];
        foreach ($postavy as $i => [$jmeno, $rasa, $povolani, $uroven]) {
            $denniky[] = [
                'id' => $i + 1,
                'jmeno' => $jmeno,
                'rasa' => $rasa,
                'povolani' => $povolani,
                'uroven' => $uroven,
                'hrac' => 'Uživatel ' . ($i + 1),
            ];
        }
        return $denniky;
    }
}
