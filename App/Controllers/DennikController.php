<?php

namespace App\Controllers;

use App\Models\Dennik;
use App\Models\DennikPovolanie;
use App\Models\Pouzivatel;
use App\Models\Povolanie;
use App\Models\RasovaSchopnost;
use App\Models\Rasa;
use Framework\Core\BaseController;
use App\Models\DennikZvlastnaSchopnost;
use App\Models\ZvlastnaSchopnost;
use Framework\Http\HttpException;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

/**
 * Class DennikController
 *
 * Spravuje denníky postáv hry Dračí doupě II:
 *  - index  - zoznam denníkov (Read),
 *  - detail - konkrétny denník postavy (Read),
 *  - update - uloženie zmien denníka (Update),
 *  - delete - zmazanie denníka (Delete),
 *  - create - formulár na vytvorenie nového denníka,
 *  - store  - uloženie nového denníka do databázy (POST).
 *
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
     * Zobrazí zoznam denníkov z databázy (voliteľne filtrovaný podľa mena postavy v parametri `q`).
     */
    public function index(Request $request): Response
    {
        $q = trim((string)$request->get('q'));
        $zaznamy = $q === ''
            ? Dennik::getAll(orderBy: 'upraveny DESC')
            : Dennik::getAll('meno_postavy LIKE ?', ['%' . $q . '%'], 'upraveny DESC');

        $rasy = [];
        foreach (Rasa::getAll() as $r) {
            $rasy[$r->idRasa] = $r->nazov;
        }
        $hraci = [];
        foreach (Pouzivatel::getAll() as $u) {
            $hraci[$u->idPouzivatel] = $u->meno;
        }
        $nazvyPovolani = [];
        foreach (Povolanie::getAll() as $p) {
            $nazvyPovolani[$p->idPovolanie] = $p->nazov;
        }
        $povolaniaDennika = [];
        foreach (DennikPovolanie::getAll('uroven > 0', [], 'uroven DESC') as $dp) {
            $povolaniaDennika[$dp->idDennik][] = $dp;
        }

        $denniky = [];
        foreach ($zaznamy as $d) {
            $povolania = $povolaniaDennika[$d->idDennik] ?? [];
            $denniky[] = [
                'id' => $d->idDennik,
                'jmeno' => $d->menoPostavy,
                'rasa' => $rasy[$d->idRasa] ?? '',
                'povolani' => $povolania ? ($nazvyPovolani[$povolania[0]->idPovolanie] ?? '') : 'bez povolání',
                'uroven' => array_sum(array_map(fn($dp) => $dp->uroven, $povolania)),
                'hrac' => $hraci[$d->idPouzivatel] ?? '',
            ];
        }
        return $this->html(['denniky' => $denniky, 'q' => $q]);
    }

    /**
     * Zobrazí denník jednej postavy podľa dát v databáze.
     */
    public function detail(Request $request): Response
    {
        $dennik = $this->nacitajDennik($request);

        $povolania = [];
        $nazvyPovolani = [];
        foreach (DennikPovolanie::getAll('id_dennik = ?', [$dennik->idDennik], 'id_dennik_povolanie') as $dp) {
            $povolanie = Povolanie::getOne($dp->idPovolanie);
            $nazvyPovolani[$dp->idPovolanie] = $povolanie?->nazov ?? '';
            $povolania[] = ['nazov' => $povolanie?->nazov ?? '', 'uroven' => $dp->uroven];
        }

        $schopnosti = [];
        foreach (DennikZvlastnaSchopnost::getAll('id_dennik = ?', [$dennik->idDennik], 'id_dennik_zvlastna_schopnost') as $dzs) {
            $zs = ZvlastnaSchopnost::getOne($dzs->idSchopnost);
            if ($zs !== null) {
                $schopnosti[] = ['nazov' => $zs->nazov, 'vlastnost' => $zs->vlastnost,
                    'povolanie' => $nazvyPovolani[$dzs->idPovolanie] ?? ''];
            }
        }

        return $this->html([
            'dennik' => $dennik,
            'rasa' => Rasa::getOne($dennik->idRasa),
            'rasovaSchopnost' => RasovaSchopnost::getOne($dennik->idRasovaSchopnost),
            'povolania' => $povolania,
            'schopnosti' => $schopnosti,
            'ulozene' => $request->get('ulozene') !== null,
        ]);
    }

    /**
     * Uloží zmeny denníka (POST): meno, vlastnosti, peniaze, vybavenie a poznámky.
     */
    public function update(Request $request): Response
    {
        $dennik = $this->nacitajDennik($request);
        if (!$request->isPost()) {
            return $this->redirect($this->url('dennik.detail', ['id' => $dennik->idDennik]));
        }

        $meno = trim((string)$request->post('menoPostavy'));
        if ($meno !== '' && mb_strlen($meno) <= 120) {
            $dennik->menoPostavy = $meno;
        }
        foreach (['telo', 'dusa', 'vplyv'] as $vlastnost) {
            // zaškrtnuté políčka majú hodnoty 1..11, uloží sa najvyššie zaškrtnuté
            $zaskrtnute = $request->post($vlastnost);
            $dennik->{$vlastnost} = is_array($zaskrtnute) && $zaskrtnute ? min(11, max(0, (int)max($zaskrtnute))) : 0;
        }
        foreach (['dukaty', 'grose', 'haliere'] as $peniaze) {
            $dennik->{$peniaze} = max(0, (int)$request->post($peniaze));
        }

        // vybavenie: 15 riadkov po 3 stĺpcoch, v DB je každý neprázdny riadok na jednom riadku "a | b | c"
        $riadky = [];
        $vybavenie = $request->post('vybavenie');
        foreach (is_array($vybavenie) ? $vybavenie : [] as $riadok) {
            if (!is_array($riadok)) {
                continue;
            }
            $bunky = array_map(fn($b) => trim(str_replace(['|', "\n", "\r"], ' ', (string)$b)), array_slice(array_values($riadok), 0, 3));
            if (implode('', $bunky) !== '') {
                $riadky[] = rtrim(implode(' | ', $bunky), ' |');
            }
        }
        $dennik->vybavenie = $riadky ? implode("\n", $riadky) : null;

        $poznamky = trim((string)$request->post('popis'));
        $dennik->popis = $poznamky !== '' ? $poznamky : null;
        $dennik->upraveny = date('Y-m-d H:i:s');
        $dennik->save();

        return $this->redirect($this->url('dennik.detail', ['id' => $dennik->idDennik, 'ulozene' => 1]));
    }

    /**
     * Zmaže denník (POST). Povolania a schopnosti denníka sa zmažú kaskádou v databáze.
     */
    public function delete(Request $request): Response
    {
        $dennik = $this->nacitajDennik($request);
        if ($request->isPost()) {
            $dennik->delete();
        }
        return $this->redirect($this->url('dennik.index'));
    }

    /**
     * Načíta denník podľa parametra `id` alebo vyhodí chybu 404.
     * TODO: po pridaní prihlasovania kontrolovať, že denník patrí prihlásenému používateľovi (alebo je verejný).
     */
    private function nacitajDennik(Request $request): Dennik
    {
        $id = (int)$request->value('id');
        $dennik = $id > 0 ? Dennik::getOne($id) : null;
        if ($dennik === null) {
            throw new HttpException(404, 'Denník neexistuje.');
        }
        return $dennik;
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
}
