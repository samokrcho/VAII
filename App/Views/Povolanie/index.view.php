<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Povolanie[] $povolania */
/** @var array<int, \App\Models\ZvlastnaSchopnost[]> $schopnosti */

// Názvy atribútov ako v pravidlách
$atributy = ['telo' => 'Tělo', 'duse' => 'Duše', 'vliv' => 'Vliv'];
?>

<main class="info flex-grow-1">
    <div class="container px-3 px-md-4 py-4 py-md-5">
        <h1 class="info__title">Povolání</h1>
        <p class="info__lead">Postava může mít každé povolání na úrovni 0 až 5. Za každou úroveň si vybere jednu zvláštní schopnost daného povolání.</p>

        <?php foreach ($povolania as $povolanie) { ?>
            <section class="info__item" aria-labelledby="povolanie-<?= (int)$povolanie->idPovolanie ?>">
                <h2 id="povolanie-<?= (int)$povolanie->idPovolanie ?>"><?= e($povolanie->nazov) ?></h2>

                <?php if ($povolanie->popis) { ?>
                    <details class="info__details">
                        <summary>Dovednosti</summary>
                        <p class="info__text"><?= nl2br(e($povolanie->popis)) ?></p>
                    </details>
                <?php } ?>

                <h3 class="info__sub">Zvláštní schopnosti</h3>
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
                    <?php foreach ($schopnosti[$povolanie->idPovolanie] ?? [] as $s) { ?>
                        <div class="col">
                            <article class="trait h-100">
                                <h3><?= e($s->nazov) ?> <span class="trait__attr">(<?= e($atributy[$s->vlastnost] ?? $s->vlastnost) ?>)</span></h3>
                                <?php if ($s->citat) { ?><p class="trait__quote"><em><?= e($s->citat) ?></em></p><?php } ?>
                                <p class="mb-0"><?= e($s->text) ?></p>
                            </article>
                        </div>
                    <?php } ?>
                </div>
            </section>
        <?php } ?>
    </div>
</main>
