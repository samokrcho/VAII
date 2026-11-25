<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Dennik $dennik */
/** @var \App\Models\Rasa|null $rasa */
/** @var \App\Models\RasovaSchopnost|null $rasovaSchopnost */
/** @var array<int, array{nazov: string, uroven: int}> $povolania */
/** @var array<int, array{text: string}> $schopnosti */
/** @var array<int, array{id: int, nazov: string}> $dostupne povolania, ktoré postava ešte nemá */
/** @var bool $ulozene */

$idUrl = ['id' => $dennik->idDennik];

/**
 * Rad 11 políčok pre vlastnosť. Aktívne (zaškrtnuté) je prvých $hodnota, políčka nad maximom
 * ($max) sú prečiarknuté a nedajú sa použiť. JS pri kliknutí drží aktívne políčka súvisle zľava.
 */
$policka = function (string $name, string $label, int $hodnota, int $max): string {
    $html = '<span class="boxes" data-max="' . $max . '" role="group" aria-label="' . e($label) . '">';
    for ($i = 1; $i <= 11; $i++) :
        $mimo = $i > $max;
        $html .= '<input type="checkbox" name="' . $name . '[]" value="' . $i . '"'
                . ($i <= $hodnota && !$mimo ? ' checked' : '') . ($mimo ? ' disabled class="is-off"' : '')
                . ' aria-label="' . e($label . ' ' . $i) . '">';
    endfor;
    return $html . '</span>';
};

/** Obsah bunky s povolaním: názov, úroveň a v režime úprav tlačidlá − + × (alebo + v prázdnom mieste). */
$bunkaPovolania = function (?array $p, int $poradie) use ($dostupne): string {
    if ($p === null) {
        $html = '<input class="plain" type="text" readonly tabindex="-1" aria-label="Povolání ' . ($poradie + 1) . '" value="">'
                . '<button type="button" class="class-add" data-act="open-picker" aria-haspopup="listbox">+ Přidat povolání</button>'
                . '<div class="picker" hidden role="listbox" aria-label="Povolání, která postava ještě nemá">';
        foreach ($dostupne as $d) :
            $html .= '<button type="button" role="option" data-act="add" data-id="' . (int)$d['id'] . '">' . e($d['nazov']) . '</button>';
        endforeach;
        return $html . ($dostupne ? '' : '<p class="picker__empty">Žádné další povolání.</p>') . '</div>';
    }
    $html = '<input class="plain" type="text" readonly tabindex="-1" aria-label="Povolání ' . ($poradie + 1) . '" value="' . e($p['nazov']) . '">'
            . '<span class="boxes boxes--narrow ro" role="group" aria-label="Úroveň povolání ' . ($poradie + 1) . '">';
    for ($i = 1; $i <= 5; $i++) :
        $html .= '<input type="checkbox" disabled' . ($i <= $p['uroven'] ? ' checked' : '') . ' aria-label="Úroveň ' . $i . '">';
    endfor;
    return $html . '</span><span class="class-ctl" data-id="' . (int)$p['id'] . '">'
            . '<button type="button" data-act="minus" aria-label="Snížit úroveň"' . ($p['uroven'] <= 1 ? ' disabled' : '') . '>−</button>'
            . '<button type="button" data-act="plus" aria-label="Zvýšit úroveň"' . ($p['uroven'] >= 5 ? ' disabled' : '') . '>+</button>'
            . '<button type="button" data-act="remove" aria-label="Odstranit povolání">×</button></span>';
};

/** Riadok zvláštnej schopnosti: získaná schopnosť, alebo miesto čakajúce na výber (tlačidlo + zoznam schopností povolania). */
$riadokSchopnosti = function (?array $sch, int $i) use ($povolania): string {
    if ($sch === null || empty($sch['pending'])) {
        return '<input class="plain" type="text" readonly tabindex="-1" aria-label="Zvláštní schopnost ' . ($i + 1) . '" value="' . e($sch['text'] ?? '') . '">';
    }
    $moznosti = [];
    foreach ($povolania as $p) :
        if ($p['id'] === $sch['idPovolanie']) :
            $moznosti = $p['moznosti'];
        endif;
    endforeach;
    $html = '<button type="button" class="ability-add" data-act="open-ability" aria-haspopup="listbox">+ Přidat zvláštní schopnost – ' . e($sch['text']) . '</button>'
            . '<div class="picker picker--abilities" hidden role="listbox" aria-label="Schopnosti povolání ' . e($sch['text']) . '">';
    foreach ($moznosti as $m) :
        $html .= '<button type="button" role="option" data-act="pick-ability" data-povolanie="' . (int)$sch['idPovolanie'] . '" data-id="' . (int)$m['id'] . '">' . e($m['nazov']) . '</button>';
    endforeach;
    return $html . ($moznosti ? '' : '<p class="picker__empty">Žádná schopnost není dostupná.</p>') . '</div>';
};

/** Stupnica 10..1 a 0 s hodnotou ukrytou vo formulárovom poli (posuvník so sponou). */
$stupnica = function (string $trieda, string $name, string $nazov, int $hodnota): string {
    $html = '<div class="scale scale--' . $trieda . '" data-scale="' . $trieda . '"><ol class="scale__nums" aria-hidden="true">';
    for ($n = 10; $n >= 0; $n--) :
        $html .= '<li data-n="' . $n . '"></li>';
    endfor;
    return $html . '</ol><p class="scale__label"><span>' . e($nazov) . '</span></p>'
            . '<input type="hidden" name="' . $name . '" value="' . $hodnota . '"></div>';
};

// riadky vybavenia: "a | b | c", vždy 15 riadkov
$vybavenie = [];
foreach (preg_split('/\R/', (string)$dennik->vybavenie, -1, PREG_SPLIT_NO_EMPTY) as $riadok) :
    $vybavenie[] = array_pad(array_map('trim', explode('|', $riadok, 3)), 3, '');
endforeach;
$vybavenie = array_pad($vybavenie, max(15, count($vybavenie)), ['', '', '']);
?>

<main class="document flex-grow-1">
    <div class="container-fluid px-3 px-lg-5 py-4">

        <?php if ($ulozene) : ?>
            <div class="alert alert-success" role="status">Deník byl uložen.</div>
        <?php endif; ?>

        <div class="d-flex flex-wrap gap-2 mb-3 diary-toolbar">
            <a class="btn btn-outline-dark" href="<?= $link->url('dennik.index') ?>">← Deníky</a>
            <!-- Smazání = samostatný formulář (POST) -->
            <form method="post" action="<?= $link->url('dennik.delete', $idUrl) ?>"
                  onsubmit="return confirm('Opravdu smazat deník postavy <?= e(addslashes($dennik->menoPostavy)) ?>?');">
                <button type="submit" class="btn btn-outline-danger">Smazat deník</button>
            </form>
        </div>

        <!-- Úpravy = jeden formulář přes oba listy (POST) -->
        <form method="post" action="<?= $link->url('dennik.update', $idUrl) ?>" id="dennik-form"
              data-url-add="<?= e($link->url('dennik.pridajPovolanie', $idUrl)) ?>"
              data-url-level="<?= e($link->url('dennik.zmenUroven', $idUrl)) ?>"
              data-url-ability="<?= e($link->url('dennik.vyberSchopnost', $idUrl)) ?>"
              data-url-remove="<?= e($link->url('dennik.odstranPovolanie', $idUrl)) ?>"
              data-stav="<?= e(json_encode(['dostupne' => $dostupne, 'povolania' => $povolania], JSON_UNESCAPED_UNICODE)) ?>">

            <!-- Přepínač režimu úprav (checkbox ve vzhledu Bootstrap tlačítka) -->
            <input type="checkbox" class="btn-check edit-toggle" id="edit-mode" autocomplete="off">
            <label class="btn btn-dark edit-button mb-4" for="edit-mode">
                <span class="edit-button__view">Upravit deník</span>
                <span class="edit-button__edit">Zrušit úpravy</span>
            </label>
            <button type="submit" class="btn btn-success edit-submit mb-4 ms-2">Uložit deník</button>

            <!-- Dvě části deníku: na malých zařízeních pod sebou, od "lg" vedle sebe -->
            <div class="pages row g-4 justify-content-center">

                <!-- ===================== LEVÁ STRANA ===================== -->
                <div class="col-12 col-lg-6 page-col">
                    <article class="page" aria-labelledby="front-title">
                        <div class="sheet sheet--front">

                            <header class="sheet__head">
                                <h1 id="front-title">Deník postavy</h1>
                                <p class="logo" aria-label="Dračí Doupě II">
                                    <span class="logo__word">Dračí</span>
                                    <span class="logo__word">Doupě</span>
                                    <span class="logo__num">II</span>
                                </p>
                            </header>

                            <section class="traits" aria-labelledby="traits-title">
                                <?= $stupnica('threat', 'ohrozenie', 'Ohrožení', min(10, (int)$dennik->ohrozenie)) ?>

                                <div class="identity">
                                    <h2 id="traits-title">Vlastnosti</h2>
                                    <label class="identity__field">Jméno: <input class="plain" type="text" name="menoPostavy" maxlength="120" required value="<?= e($dennik->menoPostavy) ?>"></label>
                                    <label class="identity__field">Rasa: <input class="plain" type="text" readonly tabindex="-1" value="<?= e($rasa?->nazov ?? '') ?>"></label>
                                </div>

                                <div class="attr">
                                    <span class="attr__name">Tělo:</span>
                                    <?= $policka('telo', 'Tělo', (int)$dennik->telo, (int)$dennik->teloMax) ?>
                                    <input class="line" type="text" readonly tabindex="-1" aria-label="Poznámka – Tělo">
                                </div>
                                <div class="attr">
                                    <span class="attr__name">Duše:</span>
                                    <?= $policka('dusa', 'Duše', (int)$dennik->dusa, (int)$dennik->dusaMax) ?>
                                    <input class="line" type="text" readonly tabindex="-1" aria-label="Poznámka – Duše">
                                </div>
                                <div class="attr">
                                    <span class="attr__name">Vliv:</span>
                                    <?= $policka('vplyv', 'Vliv', (int)$dennik->vplyv, (int)$dennik->vplyvMax) ?>
                                    <input class="line" type="text" readonly tabindex="-1" aria-label="Poznámka – Vliv">
                                </div>

                                <div class="bonus">
                                    <h2>Bonusy / Postihy</h2>
                                    <input class="line" type="text" readonly tabindex="-1" aria-label="Bonus nebo postih 1">
                                    <input class="line" type="text" readonly tabindex="-1" aria-label="Bonus nebo postih 2">
                                </div>

                                <div class="classes" id="povolania-box">
                                    <div class="class-row class-row--head">
                                        <h2>Povolání</h2>
                                        <h2 class="class-row__trait-title">Rasová schopnost:</h2>
                                    </div>

                                    <?php for ($r = 0; $r < 3; $r++) :
                                        $p = $povolania[$r] ?? null; ?>
                                        <div class="class-row">
                                            <div class="class-cell class-cell--job"><?= $bunkaPovolania($p, $r) ?></div>
                                            <?php if ($r === 0) : ?>
                                                <div class="class-cell class-cell--trait">
                                                    <input class="plain" type="text" readonly tabindex="-1" aria-label="Rasová schopnost" value="<?= e($rasovaSchopnost?->nazov ?? '') ?>">
                                                </div>
                                                <p class="class-row__hint">Úroveň</p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </section>

                            <section class="abilities" aria-labelledby="abilities-title">
                                <div class="abilities__head">
                                    <h2 id="abilities-title">Zvláštní schopnosti</h2>
                                    <span class="abilities__col abilities__col--desc">popis</span>
                                    <span class="abilities__col abilities__col--act">aktivace</span>
                                    <span class="abilities__col abilities__col--job">povolání</span>
                                </div>
                                <div class="abilities__body">
                                    <?= $stupnica('advantage', 'vyhoda', 'Výhoda', min(10, (int)$dennik->vyhoda)) ?>
                                    <ul class="abilities__list">
                                        <?php for ($i = 0; $i < 19; $i++) :
                                            $s = $schopnosti[$i] ?? null; ?>
                                            <li><?= $riadokSchopnosti($s, $i) ?></li>
                                        <?php endfor; ?>
                                    </ul>
                                </div>
                            </section>

                            <div class="sheet__foot" aria-hidden="true">
                                <span class="rule rule--left"></span>
                                <img class="sword" src="<?= $link->asset('images/sword.svg') ?>" alt="" width="304.5" height="64">
                                <span class="rule rule--right"></span>
                            </div>
                        </div>
                        <!-- Sponky Ohrožení / Výhoda: zadní půlka je pod listem, přední nad ním -->
                        <?php foreach (['threat' => 'Ohrožení', 'advantage' => 'Výhoda'] as $klic => $nazov) : ?>
                            <div class="clip clip--<?= $klic ?>" data-scale="<?= $klic ?>" role="slider" tabindex="0"
                                 aria-label="<?= e($nazov) ?>" aria-valuemin="0" aria-valuemax="10" aria-valuenow="0">
                                <svg class="clip__part clip__back" viewBox="0 0 78 26" aria-hidden="true" focusable="false">
                                    <path d="M26 3.5H14a9.5 9.5 0 0 0 0 19h12M26 8.5H15.5a4.5 4.5 0 0 0 0 9H26"/>
                                </svg>
                                <svg class="clip__part clip__front" viewBox="0 0 78 26" aria-hidden="true" focusable="false">
                                    <path d="M26 3.5h42a9.5 9.5 0 0 1 0 19H26"/>
                                </svg>
                            </div>
                        <?php endforeach; ?>
                    </article>
                </div>

                <!-- ===================== PRAVÁ STRANA ===================== -->
                <div class="col-12 col-lg-6 page-col">
                    <article class="page" aria-label="Vybavení a poznámky">
                        <div class="sheet sheet--back">

                            <div class="money">
                                <label class="money__label" for="dukaty">Peníze:</label>
                                <input class="line" id="dukaty" type="text" inputmode="numeric" pattern="[0-9]*" name="dukaty" aria-label="Dukátů" value="<?= (int)$dennik->dukaty ?>">
                                <span class="money__unit">dukátů,</span>
                                <input class="line" type="text" inputmode="numeric" pattern="[0-9]*" name="grose" aria-label="Grošů" value="<?= (int)$dennik->grose ?>">
                                <span class="money__unit">grošů,</span>
                                <input class="line" type="text" inputmode="numeric" pattern="[0-9]*" name="haliere" aria-label="Haléřů" value="<?= (int)$dennik->haliere ?>">
                                <span class="money__unit">haléřů</span>
                            </div>

                            <section class="equipment" aria-labelledby="equip-title">
                                <div class="equipment__head">
                                    <h2 id="equip-title">Vybavení</h2>
                                    <p class="equipment__load">Naložení:</p>
                                    <p class="equipment__lst">L / S / T</p>
                                </div>
                                <ul class="equipment__list">
                                    <?php foreach ($vybavenie as $i => $v) : ?>
                                        <li>
                                            <input class="line" type="text" name="vybavenie[<?= $i ?>][a]" aria-label="Vybavení <?= $i + 1 ?>" value="<?= e($v[0]) ?>">
                                            <input class="line" type="text" name="vybavenie[<?= $i ?>][b]" aria-label="Vybavení <?= $i + 1 ?> – doplněk" value="<?= e($v[1]) ?>">
                                            <input class="line" type="text" name="vybavenie[<?= $i ?>][c]" aria-label="Vybavení <?= $i + 1 ?> – poznámka" value="<?= e($v[2]) ?>">
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </section>

                            <section class="notes" aria-labelledby="notes-title">
                                <h2 id="notes-title">Poznámky</h2>
                                <div class="notes__body">
                                    <ul class="notes__rules" aria-hidden="true"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul>
                                    <label>
                                        <textarea name="popis" aria-labelledby="notes-title"><?= e($dennik->popis) ?></textarea>
                                    </label>
                                </div>
                            </section>

                            <div class="sheet__foot" aria-hidden="true">
                                <span class="rule rule--left"></span>
                                <img class="sword" src="<?= $link->asset('images/sword.svg') ?>" alt="" width="304.5" height="64">
                                <span class="rule rule--right"></span>
                            </div>
                        </div>
                    </article>
                </div>

            </div>
        </form>
    </div>
</main>
