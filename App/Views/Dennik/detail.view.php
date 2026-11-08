<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Dennik $dennik */
/** @var \App\Models\Rasa|null $rasa */
/** @var \App\Models\RasovaSchopnost|null $rasovaSchopnost */
/** @var array<int, array{nazov: string, uroven: int}> $povolania */
/** @var array<int, array{nazov: string, vlastnost: string, povolanie: string}> $schopnosti */
/** @var bool $ulozene */

$atributy = ['telo' => 'Tělo', 'duse' => 'Duše', 'vliv' => 'Vliv'];
$idUrl = ['id' => $dennik->idDennik];

/** Rad 11 políčok pre vlastnosť (zaškrtnuté sú prvé $hodnota). */
$policka = function (string $name, string $label, int $hodnota): string {
    $html = '<span class="boxes" role="group" aria-label="' . e($label) . '">';
    for ($i = 1; $i <= 11; $i++) :
        $html .= '<input type="checkbox" name="' . $name . '[]" value="' . $i . '"'
            . ($i <= $hodnota ? ' checked' : '') . ' aria-label="' . e($label . ' ' . $i) . '">';
    endfor;
    return $html . '</span>';
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
        <form method="post" action="<?= $link->url('dennik.update', $idUrl) ?>">

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
                                <div class="scale scale--threat">
                                    <ol class="scale__nums" reversed aria-hidden="true"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ol>
                                    <p class="scale__label"><span>Ohrožení: <?= (int)$dennik->ohrozenie ?></span></p>
                                </div>

                                <div class="identity">
                                    <h2 id="traits-title">Vlastnosti</h2>
                                    <label class="identity__field">Jméno: <input class="plain" type="text" name="menoPostavy" maxlength="120" required value="<?= e($dennik->menoPostavy) ?>"></label>
                                    <label class="identity__field">Rasa: <input class="plain" type="text" readonly tabindex="-1" value="<?= e($rasa?->nazov ?? '') ?>"></label>
                                </div>

                                <div class="attr">
                                    <span class="attr__name">Tělo:</span>
                                    <?= $policka('telo', 'Tělo', (int)$dennik->telo) ?>
                                    <input class="line" type="text" readonly tabindex="-1" aria-label="Poznámka – Tělo">
                                </div>
                                <div class="attr">
                                    <span class="attr__name">Duše:</span>
                                    <?= $policka('dusa', 'Duše', (int)$dennik->dusa) ?>
                                    <input class="line" type="text" readonly tabindex="-1" aria-label="Poznámka – Duše">
                                </div>
                                <div class="attr">
                                    <span class="attr__name">Vliv:</span>
                                    <?= $policka('vplyv', 'Vliv', (int)$dennik->vplyv) ?>
                                    <input class="line" type="text" readonly tabindex="-1" aria-label="Poznámka – Vliv">
                                </div>

                                <div class="bonus">
                                    <h2>Bonusy / Postihy</h2>
                                    <input class="line" type="text" readonly tabindex="-1" aria-label="Bonus nebo postih 1">
                                    <input class="line" type="text" readonly tabindex="-1" aria-label="Bonus nebo postih 2">
                                </div>

                                <div class="classes">
                                    <div class="class-row class-row--head">
                                        <h2>Povolání</h2>
                                        <h2 class="class-row__trait-title">Rasová schopnost:</h2>
                                    </div>

                                    <?php for ($r = 0; $r < 3; $r++) :
                                        $p = $povolania[$r] ?? null; ?>
                                        <div class="class-row">
                                            <div class="class-cell class-cell--job">
                                                <input class="plain" type="text" readonly tabindex="-1" aria-label="Povolání <?= $r + 1 ?>" value="<?= e($p['nazov'] ?? '') ?>">
                                                <span class="boxes boxes--narrow ro" role="group" aria-label="Úroveň povolání <?= $r + 1 ?>">
                                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                        <input type="checkbox" disabled<?= $p && $i <= $p['uroven'] ? ' checked' : '' ?> aria-label="Úroveň <?= $i ?>">
                                                    <?php endfor; ?>
                                                </span>
                                            </div>
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
                                    <div class="scale scale--advantage">
                                        <ol class="scale__nums" reversed aria-hidden="true"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ol>
                                        <p class="scale__label"><span>Výhoda: <?= (int)$dennik->vyhoda ?></span></p>
                                    </div>
                                    <ul class="abilities__list">
                                        <?php for ($i = 0; $i < 19; $i++) :
                                            $s = $schopnosti[$i] ?? null; ?>
                                            <li><input class="plain" type="text" readonly tabindex="-1" aria-label="Zvláštní schopnost <?= $i + 1 ?>"
                                                       value="<?= $s ? e($s['nazov'] . ' (' . ($atributy[$s['vlastnost']] ?? $s['vlastnost']) . ')' . ($s['povolanie'] !== '' ? ' – ' . $s['povolanie'] : '')) : '' ?>"></li>
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
