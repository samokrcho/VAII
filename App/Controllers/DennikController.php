<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

/**
 * Class DennikController
 *
 * Spravuje denníky postáv hry Dračí doupě II:
 *  - index  - zoznam denníkov (dennik_list),
 *  - detail - konkrétny denník postavy (dennik).
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
