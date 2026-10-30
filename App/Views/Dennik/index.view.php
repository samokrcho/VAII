<?php

/** @var \Framework\Support\LinkGenerator $link */
?>

<main class="diaries flex-grow-1">
    <div class="diaries__inner container-fluid px-3 px-md-4">

        <h1 class="diaries__title" aria-label="Dračí Doupě II">
            <span class="diaries__fraktur">Dračí Doupě</span>
            <span class="diaries__roman">II</span>
        </h1>

        <!-- Ovládací lišta (zatím jen vizuální) -->
        <form class="row g-2 mb-4" role="search" action="<?= $link->url('dennik.index') ?>">
            <div class="col-12 col-md">
                <input class="form-control" type="search" name="q" placeholder="Hledat podle jména postavy…" aria-label="Hledat deník">
            </div>
            <div class="col-6 col-md-auto">
                <select class="form-select" name="povolani" aria-label="Filtrovat podle povolání">
                    <option>Všechna povolání</option><option>Bojovník</option><option>Zaříkavač</option><option>Kejklíř</option><option>Lovec</option><option>Mastičkár</option>
                </select>
            </div>
            <div class="col-6 col-md-auto">
                <select class="form-select" name="zobrazit" aria-label="Zobrazit deníky">
                    <option>Všechny deníky</option><option>Moje deníky</option><option>Deníky spoluhráčů</option>
                </select>
            </div>
            <div class="col-12 col-md-auto">
                <button type="button" class="btn btn-dark w-100">+ Nový deník</button>
            </div>
        </form>

        <!-- Seznam deníků: 2 sloupce na mobilu, 3 na tabletu, 4 na desktopu -->
        <section aria-label="Deníky postav">
            <ul class="row row-cols-2 row-cols-lg-3 row-cols-xl-4 g-3 g-md-4 list-unstyled mb-0">
                <?php foreach ($denniky as $dennik) { ?>
                    <li class="col">
                        <a class="diary d-block text-decoration-none"
                           href="<?= $link->url('dennik.detail', ['id' => $dennik['id']]) ?>">
                            <img class="diary__img img-fluid w-100"
                                 src="<?= $link->asset('images/dennik_placeholder.png') ?>"
                                 alt="Náhled deníku: <?= e($dennik['jmeno']) ?>" width="442" height="584">
                            <span class="diary__meta d-block mt-2 px-1">
                                <strong class="d-block"><?= e($dennik['jmeno']) ?></strong>
                                <span class="d-block fst-italic">
                                    <?= e($dennik['rasa']) ?> · <?= e($dennik['povolani']) ?> · úr. <?= e($dennik['uroven']) ?>
                                </span>
                                <small class="d-block">Hráč: <?= e($dennik['hrac']) ?></small>
                            </span>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </section>

    </div>
</main>
