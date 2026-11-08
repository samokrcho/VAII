<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Rasa[] $rasy */
/** @var \App\Models\RasovaSchopnost[] $rasoveSchopnosti */
/** @var \App\Models\Povolanie[] $povolania */
/** @var array<string, string> $errors */
/** @var array<string, mixed> $old */

$val = fn(string $k) => $old[$k] ?? '';
$invalid = fn(string $k) => isset($errors[$k]) ? ' is-invalid' : '';
$err = function (string $k) use ($errors) {
    return isset($errors[$k]) ? '<div class="invalid-feedback">' . e($errors[$k]) . '</div>' : '';
};
?>

<main class="info flex-grow-1">
    <div class="container px-3 px-md-4 py-4 py-md-5" style="max-width: 760px;">
        <h1 class="info__title">Nový deník</h1>
        <p class="info__lead">Vyplň základ postavy. Vlastnosti, výbavu a schopnosti doplníš později přímo v deníku.</p>

        <form method="post" action="<?= $link->url('dennik.store') ?>" novalidate>
            <div class="mb-3">
                <label class="form-label" for="menoPostavy">Jméno postavy</label>
                <input class="form-control<?= $invalid('menoPostavy') ?>" type="text" id="menoPostavy" name="menoPostavy"
                       maxlength="120" required value="<?= e($val('menoPostavy')) ?>">
                <?= $err('menoPostavy') ?>
            </div>

            <div class="mb-3">
                <label class="form-label" for="idRasa">Rasa</label>
                <select class="form-select<?= $invalid('idRasa') ?>" id="idRasa" name="idRasa" required>
                    <option value="">– vyber rasu –</option>
                    <?php foreach ($rasy as $rasa) { ?>
                        <option value="<?= (int)$rasa->idRasa ?>"<?= (string)$val('idRasa') === (string)$rasa->idRasa ? ' selected' : '' ?>><?= e($rasa->nazov) ?></option>
                    <?php } ?>
                </select>
                <?= $err('idRasa') ?>
            </div>

            <div class="mb-3">
                <label class="form-label" for="idRasovaSchopnost">Rasová schopnost</label>
                <select class="form-select<?= $invalid('idRasovaSchopnost') ?>" id="idRasovaSchopnost" name="idRasovaSchopnost" required aria-describedby="rs-help">
                    <option value="">– vyber rasovou schopnost –</option>
                    <?php
                    $skupina = null;
                    foreach ($rasoveSchopnosti as $rs) {
                        if ($rs->idRasa !== $skupina) {
                            if ($skupina !== null) { echo '</optgroup>'; }
                            $skupina = $rs->idRasa;
                            $nazovRasy = '';
                            foreach ($rasy as $r) { if ((int)$r->idRasa === $rs->idRasa) { $nazovRasy = $r->nazov; } }
                            echo '<optgroup label="' . e($nazovRasy) . '">';
                        } ?>
                        <option value="<?= (int)$rs->idRasovaSchopnost ?>"<?= (string)$val('idRasovaSchopnost') === (string)$rs->idRasovaSchopnost ? ' selected' : '' ?>><?= e($rs->nazov) ?></option>
                    <?php }
                    if ($skupina !== null) { echo '</optgroup>'; } ?>
                </select>
                <div id="rs-help" class="form-text">Schopnost musí patřit k vybrané rase.</div>
                <?= $err('idRasovaSchopnost') ?>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-8">
                    <label class="form-label" for="idPovolanie">První povolání (nepovinné)</label>
                    <select class="form-select<?= $invalid('idPovolanie') ?>" id="idPovolanie" name="idPovolanie">
                        <option value="">– žádné –</option>
                        <?php foreach ($povolania as $p) { ?>
                            <option value="<?= (int)$p->idPovolanie ?>"<?= (string)$val('idPovolanie') === (string)$p->idPovolanie ? ' selected' : '' ?>><?= e($p->nazov) ?></option>
                        <?php } ?>
                    </select>
                    <?= $err('idPovolanie') ?>
                </div>
                <div class="col-12 col-sm-4">
                    <label class="form-label" for="uroven">Úroveň</label>
                    <select class="form-select<?= $invalid('uroven') ?>" id="uroven" name="uroven">
                        <?php for ($i = 1; $i <= 5; $i++) { ?>
                            <option value="<?= $i ?>"<?= (int)($val('uroven') ?: 1) === $i ? ' selected' : '' ?>><?= $i ?></option>
                        <?php } ?>
                    </select>
                    <?= $err('uroven') ?>
                </div>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="jeVerejny" name="jeVerejny" value="1"<?= $val('jeVerejny') ? ' checked' : '' ?>>
                <label class="form-check-label" for="jeVerejny">Veřejný deník (uvidí ho i nepřihlášení)</label>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark">Vytvořit deník</button>
                <a class="btn btn-outline-dark" href="<?= $link->url('dennik.index') ?>">Zpět</a>
            </div>
        </form>
    </div>
</main>
