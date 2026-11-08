<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Rasa[] $rasy */
/** @var array<int, \App\Models\RasovaSchopnost[]> $schopnosti */

// Názvy atribútov ako v pravidlách
$atributy = ['telo' => 'Tělo', 'duse' => 'Duše', 'vliv' => 'Vliv'];
?>

<main class="info flex-grow-1">
    <div class="container px-3 px-md-4 py-4 py-md-5">
        <h1 class="info__title">Rasy</h1>
        <p class="info__lead">Rasa určuje původ postavy. Každá rasa nabízí vlastní rasové schopnosti – při tvorbě deníku si hráč vybírá jednu z nich.</p>

        <?php foreach ($rasy as $rasa) { ?>
            <section class="info__item" aria-labelledby="rasa-<?= (int)$rasa->idRasa ?>">
                <h2 id="rasa-<?= (int)$rasa->idRasa ?>"><?= e($rasa->nazov) ?></h2>
                <?php if ($rasa->popis) { ?><p class="fst-italic"><?= e($rasa->popis) ?></p><?php } ?>

                <h3 class="info__sub">Rasové schopnosti</h3>
                <div class="row row-cols-1 row-cols-md-2 g-3">
                    <?php foreach ($schopnosti[$rasa->idRasa] ?? [] as $s) { ?>
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
