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

        $stav = $this->stav($dennik);

        return $this->html([
            'dennik' => $dennik,
            'rasa' => Rasa::getOne($dennik->idRasa),
            'rasovaSchopnost' => RasovaSchopnost::getOne($dennik->idRasovaSchopnost),
            'povolania' => $stav['povolania'],
            'schopnosti' => $stav['schopnosti'],
            'dostupne' => $stav['dostupne'],
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
            // zaškrtnuté políčka majú hodnoty 1..max, uloží sa najvyššie zaškrtnuté (nikdy nad maximom)
            $zaskrtnute = $request->post($vlastnost);
            $hodnota = is_array($zaskrtnute) && $zaskrtnute ? (int)max($zaskrtnute) : 0;
            $dennik->{$vlastnost} = min((int)$dennik->{$vlastnost . 'Max'}, max(0, $hodnota));
        }
        // Ohrozenie a Výhoda (posuvník so sponou): 0..10
        $dennik->ohrozenie = min(10, max(0, (int)$request->post('ohrozenie')));
        $dennik->vyhoda = min(10, max(0, (int)$request->post('vyhoda')));
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

    /** Najvyššia úroveň povolania, ktorú môže postava mať. */
    private const MAX_UROVEN = 5;
    private const VLASTNOSTI = ['telo' => 'Tělo', 'duse' => 'Duše', 'vliv' => 'Vliv'];
    /** Počet miest na povolania v denníku. */
    private const MAX_POVOLANI = 3;

    /**
     * Povolania, získané schopnosti, schopnosti čakajúce na výber a ešte nepridané povolania denníka
     * (podklad pre detail aj pre JSON odpovede).
     *
     * Povolanie na úrovni N má N miest na schopnosti. Každé miesto, ktoré ešte nemá vybranú schopnosť,
     * sa v zozname schopností zobrazí ako "pending" riadok a hráč si môže vybrať len zo schopností tohto povolania.
     *
     * @return array{povolania: array, schopnosti: array, dostupne: array}
     */
    private function stav(Dennik $dennik): array
    {
        $povolania = [];
        $schopnosti = [];
        $cakajuce = [];
        $mame = [];
        foreach (DennikPovolanie::getAll('id_dennik = ?', [$dennik->idDennik], 'id_dennik_povolanie') as $dp) {
            $povolanie = Povolanie::getOne($dp->idPovolanie);
            $nazov = $povolanie?->nazov ?? '';
            $mame[$dp->idPovolanie] = true;

            $ziskane = DennikZvlastnaSchopnost::getAll('id_dennik = ? AND id_povolanie = ?', [$dennik->idDennik, $dp->idPovolanie], 'uroven_ziskania');
            $volne = $this->volneUrovne((int)$dp->uroven, $ziskane);
            $zoSchopnosti = [];
            foreach ($ziskane as $dzs) {
                $zs = ZvlastnaSchopnost::getOne($dzs->idSchopnost);
                if ($zs !== null) {
                    $zoSchopnosti[$zs->idSchopnost] = true;
                    $schopnosti[] = ['id' => (int)$zs->idSchopnost, 'text' => $zs->nazov . ' (' . (self::VLASTNOSTI[$zs->vlastnost] ?? $zs->vlastnost)
                        . ')' . ($nazov !== '' ? ' – ' . $nazov : '')];
                }
            }

            // schopnosti povolania, ktoré postava ešte nemá a ktoré sa dajú zaradiť na niektoré voľné miesto
            $moznosti = [];
            foreach (ZvlastnaSchopnost::getAll('id_povolanie = ?', [$dp->idPovolanie], 'minimalna_uroven, id_schopnost') as $zs) {
                if (!isset($zoSchopnosti[$zs->idSchopnost]) && $this->vhodneMiesto($volne, (int)$zs->minimalnaUroven) !== null) {
                    $moznosti[] = ['id' => (int)$zs->idSchopnost,
                        'nazov' => $zs->nazov . ' (' . (self::VLASTNOSTI[$zs->vlastnost] ?? $zs->vlastnost) . ')',
                        'od' => (int)$zs->minimalnaUroven];
                }
            }
            foreach ($volne as $_) {
                $cakajuce[] = ['pending' => true, 'idPovolanie' => (int)$dp->idPovolanie, 'text' => $nazov];
            }
            $povolania[] = ['id' => (int)$dp->idPovolanie, 'nazov' => $nazov, 'uroven' => (int)$dp->uroven, 'moznosti' => $moznosti];
        }

        $dostupne = [];
        foreach (Povolanie::getAll(orderBy: 'id_povolanie') as $p) {
            if (!isset($mame[$p->idPovolanie])) {
                $dostupne[] = ['id' => (int)$p->idPovolanie, 'nazov' => $p->nazov];
            }
        }
        return ['povolania' => $povolania, 'schopnosti' => array_merge($schopnosti, $cakajuce), 'dostupne' => $dostupne];
    }

    /**
     * Voľné úrovne (miesta na schopnosti) povolania: 1..úroveň povolania bez už obsadených.
     *
     * @param DennikZvlastnaSchopnost[] $ziskane
     * @return int[]
     */
    private function volneUrovne(int $uroven, array $ziskane): array
    {
        $obsadene = array_map(fn($dzs) => (int)$dzs->urovenZiskania, $ziskane);
        return array_values(array_diff(range(1, $uroven), $obsadene));
    }

    /** Najnižšia voľná úroveň, na ktorej sa dá schopnosť s danou minimálnou úrovňou získať (alebo null). */
    private function vhodneMiesto(array $volne, int $minimalna): ?int
    {
        foreach ($volne as $uroven) {
            if ($uroven >= $minimalna) {
                return $uroven;
            }
        }
        return null;
    }

    /**
     * Pridá povolanie (POST: id, idPovolanie) na úrovni 1. Schopnosť si hráč vyberie sám. Vráti JSON so stavom.
     */
    public function pridajPovolanie(Request $request): Response
    {
        $dennik = $this->nacitajDennik($request);
        $idPovolanie = (int)$request->post('idPovolanie');
        $mame = DennikPovolanie::getAll('id_dennik = ?', [$dennik->idDennik]);

        if (!$request->isPost() || Povolanie::getOne($idPovolanie) === null) {
            return $this->chyba('Neznámé povolání.');
        }
        if (count($mame) >= self::MAX_POVOLANI) {
            return $this->chyba('Postava už má nejvyšší počet povolání.');
        }
        foreach ($mame as $dp) {
            if ((int)$dp->idPovolanie === $idPovolanie) {
                return $this->chyba('Postava už toto povolání má.');
            }
        }

        $dp = new DennikPovolanie();
        $dp->idDennik = (int)$dennik->idDennik;
        $dp->idPovolanie = $idPovolanie;
        $dp->uroven = 1;
        $dp->save();
        return $this->json($this->stav($dennik));
    }

    /**
     * Vyberie zvláštnu schopnosť povolania (POST: id, idPovolanie, idSchopnost). Schopnosť musí patriť k povolaniu,
     * nesmie byť už získaná a musí sa dať zaradiť na voľné miesto. Vráti JSON so stavom.
     */
    public function vyberSchopnost(Request $request): Response
    {
        $dennik = $this->nacitajDennik($request);
        $dp = $this->nacitajDennikPovolanie($dennik, (int)$request->post('idPovolanie'));
        $zs = ZvlastnaSchopnost::getOne((int)$request->post('idSchopnost'));
        if (!$request->isPost() || $dp === null || $zs === null || (int)$zs->idPovolanie !== (int)$dp->idPovolanie) {
            return $this->chyba('Tuto schopnost si nelze vybrat (musí patřit k povolání).');
        }

        $ziskane = DennikZvlastnaSchopnost::getAll('id_dennik = ? AND id_povolanie = ?', [$dennik->idDennik, $dp->idPovolanie]);
        foreach ($ziskane as $dzs) {
            if ((int)$dzs->idSchopnost === (int)$zs->idSchopnost) {
                return $this->chyba('Tuto schopnost už postava má.');
            }
        }
        $uroven = $this->vhodneMiesto($this->volneUrovne((int)$dp->uroven, $ziskane), (int)$zs->minimalnaUroven);
        if ($uroven === null) {
            return $this->chyba('Schopnost zatím není dostupná – povolání má nízkou úroveň nebo už nemá volné místo.');
        }

        $dzs = new DennikZvlastnaSchopnost();
        $dzs->idDennik = (int)$dennik->idDennik;
        $dzs->idPovolanie = (int)$dp->idPovolanie;
        $dzs->idSchopnost = (int)$zs->idSchopnost;
        $dzs->urovenZiskania = $uroven;
        $dzs->save();
        return $this->json($this->stav($dennik));
    }

    /**
     * Zmení úroveň povolania (POST: id, idPovolanie, smer = 1 alebo -1).
     *
     * Zvýšenie len otvorí nové miesto na schopnosť. Pri znížení, ak sú všetky miesta obsadené, musí hráč
     * vybrať schopnosť, ktorá zanikne (parameter idSchopnost) – inak server odpovie 409 so zoznamom schopností.
     */
    public function zmenUroven(Request $request): Response
    {
        $dennik = $this->nacitajDennik($request);
        $dp = $this->nacitajDennikPovolanie($dennik, (int)$request->post('idPovolanie'));
        $smer = (int)$request->post('smer') >= 0 ? 1 : -1;
        if (!$request->isPost() || $dp === null) {
            return $this->chyba('Povolání nenalezeno.');
        }

        $nova = (int)$dp->uroven + $smer;
        if ($nova < 1 || $nova > self::MAX_UROVEN) {
            return $this->chyba($nova < 1 ? 'Nižší úroveň už není možná – povolání lze odstranit.' : 'Nejvyšší úroveň je ' . self::MAX_UROVEN . '.');
        }

        if ($smer < 0) {
            $ziskane = DennikZvlastnaSchopnost::getAll('id_dennik = ? AND id_povolanie = ?', [$dennik->idDennik, $dp->idPovolanie], 'uroven_ziskania');
            if (count($ziskane) > $nova) {
                // jedna schopnosť musí zaniknúť – vyberie ju hráč
                $zrusit = (int)$request->post('idSchopnost');
                $zostane = array_values(array_filter($ziskane, fn($dzs) => (int)$dzs->idSchopnost !== $zrusit));
                if (count($zostane) === count($ziskane)) {
                    return $this->json([
                        'volba' => ['idPovolanie' => (int)$dp->idPovolanie, 'schopnosti' => $this->schopnostiPovolania($ziskane)],
                    ])->setStatusCode(409);
                }
                $presuny = $this->naplanujPresuny($zostane, $nova);
                if ($presuny === null) {
                    return $this->chyba('Po odebrání této schopnosti by jiná nešla zachovat na nižší úrovni. Vyber jinou schopnost.');
                }
                foreach ($ziskane as $dzs) {
                    if ((int)$dzs->idSchopnost === $zrusit) {
                        $dzs->delete();
                    }
                }
                // schopnosti nad novou úrovňou se presunú na voľné nižšie úrovne
                foreach ($presuny as [$dzs, $uroven]) {
                    $dzs->urovenZiskania = $uroven;
                    $dzs->save();
                }
            } else {
                // chýbajúce miesto nie je obsadené, stačí zrušiť schopnosti nad novou úrovňou (ak by nejaké boli)
                $presuny = $this->naplanujPresuny($ziskane, $nova);
                if ($presuny === null) {
                    return $this->chyba('Povolání nelze snížit – schopnost by nešla zachovat.');
                }
                foreach ($presuny as [$dzs, $uroven]) {
                    $dzs->urovenZiskania = $uroven;
                    $dzs->save();
                }
            }
        }
        $dp->uroven = $nova;
        $dp->save();
        return $this->json($this->stav($dennik));
    }

    /**
     * Naplánuje, ktoré schopnosti s úrovňou získania nad $nova treba presunúť na voľnú nižšiu úroveň
     * (nie pod minimálnu úroveň schopnosti). Vráti null, ak sa to nedá.
     *
     * @param DennikZvlastnaSchopnost[] $schopnosti
     * @return array<int, array{0: DennikZvlastnaSchopnost, 1: int}>|null
     */
    private function naplanujPresuny(array $schopnosti, int $nova): ?array
    {
        $obsadene = [];
        foreach ($schopnosti as $dzs) {
            if ((int)$dzs->urovenZiskania <= $nova) {
                $obsadene[(int)$dzs->urovenZiskania] = true;
            }
        }
        $presuny = [];
        foreach ($schopnosti as $dzs) {
            if ((int)$dzs->urovenZiskania <= $nova) {
                continue;
            }
            $min = (int)(ZvlastnaSchopnost::getOne($dzs->idSchopnost)?->minimalnaUroven ?? 1);
            $cielova = null;
            for ($u = $nova; $u >= max(1, $min); $u--) {
                if (!isset($obsadene[$u])) {
                    $cielova = $u;
                    break;
                }
            }
            if ($cielova === null) {
                return null;
            }
            $obsadene[$cielova] = true;
            $presuny[] = [$dzs, $cielova];
        }
        return $presuny;
    }

    /** @param DennikZvlastnaSchopnost[] $ziskane  @return array<int, array{id: int, nazov: string}> */
    private function schopnostiPovolania(array $ziskane): array
    {
        $vysledok = [];
        foreach ($ziskane as $dzs) {
            $zs = ZvlastnaSchopnost::getOne($dzs->idSchopnost);
            if ($zs !== null) {
                $vysledok[] = ['id' => (int)$zs->idSchopnost, 'nazov' => $zs->nazov . ' (' . (self::VLASTNOSTI[$zs->vlastnost] ?? $zs->vlastnost) . ')'];
            }
        }
        return $vysledok;
    }

    /**
     * Odstráni povolanie aj jeho schopnosti (POST: id, idPovolanie). Vráti JSON so stavom.
     */
    public function odstranPovolanie(Request $request): Response
    {
        $dennik = $this->nacitajDennik($request);
        $dp = $this->nacitajDennikPovolanie($dennik, (int)$request->post('idPovolanie'));
        if (!$request->isPost() || $dp === null) {
            return $this->chyba('Povolání nenalezeno.');
        }
        // schopnosti povolania sa mažú kaskádou (cudzí kľúč dennik_zvlastna_schopnost -> dennik_povolanie)
        $dp->delete();
        return $this->json($this->stav($dennik));
    }

    private function nacitajDennikPovolanie(Dennik $dennik, int $idPovolanie): ?DennikPovolanie
    {
        return DennikPovolanie::getAll('id_dennik = ? AND id_povolanie = ?', [$dennik->idDennik, $idPovolanie])[0] ?? null;
    }

    /** JSON chyba s HTTP kódom 422 (JS ju zobrazí používateľovi). */
    private function chyba(string $sprava): Response
    {
        return $this->json(['chyba' => $sprava])->setStatusCode(422);
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
        // 15 bodov zdatnosti rozdelených rovnomerne (5/5/5), zdroje sú na začiatku plné
        $dennik->telo = $dennik->teloMax = $dennik->dusa = $dennik->dusaMax = $dennik->vplyv = $dennik->vplyvMax = 5;
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
