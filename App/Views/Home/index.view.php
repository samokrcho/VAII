<?php

/** @var \Framework\Support\LinkGenerator $link */
?>

<main class="home flex-grow-1">
    <div class="container px-3 px-md-4 py-4 py-md-5">

        <header class="home__hero text-center">
            <h1 class="home__title" aria-label="Dračí Doupě II">
                <span class="diaries__fraktur">Dračí Doupě</span>
                <span class="diaries__roman">II</span>
            </h1>
            <p class="home__lead">Česká hra na hrdiny. Správce deníků postav na jednom místě.</p>
        </header>

        <div class="row g-4 g-lg-5">
            <div class="col-12 col-lg-7">
                <section class="home__section" aria-labelledby="co-je">
                    <h2 id="co-je">Co je Dračí doupě</h2>
                    <p>Dračí doupě je nejznámější a nejrozšířenější česká hra na hrdiny. První vydání vyšlo v roce 1990. Nová podoba, <strong>Dračí doupě II</strong>, vyšla v roce 2011 a v roce 2017 se dočkala druhého, přepracovaného vydání.</p>
                </section>

                <section class="home__section" aria-labelledby="jak-se-hraje">
                    <h2 id="jak-se-hraje">Jak se hraje</h2>
                    <p>Hra na hrdiny je společné vyprávění příběhu. Nejde v ní o vítězství, ale o to prožít dobrodružství svých postav a dobře se při tom bavit. Příběh není předem daný, hráči ho svými rozhodnutími tvoří.</p>
                    <ul>
                        <li><strong>Průvodce hrou</strong> vymyslí zápletku, popisuje svět a hraje všechny ostatní bytosti. Je spíš pomocníkem hráčů než jejich pánem.</li>
                        <li><strong>Hráči</strong> hrají své <strong>hrdiny</strong>. Rozhodují, jakou rasu a povolání mají, co dokážou a jak se v příběhu zachovají.</li>
                        <li><strong>Pravidla</strong> slouží jako vodítko. Podle nich se pozná, co se postavě povede a co ji to bude stát.</li>
                    </ul>
                </section>

                <section class="home__section" aria-labelledby="denik-postavy">
                    <h2 id="denik-postavy">Deník postavy</h2>
                    <p>Každý hráč má deník své postavy. Zapisuje do něj vlastnosti (Tělo, Duše, Vliv), Ohrožení a Výhodu, rasovou schopnost, povolání a jejich úrovně, zvláštní schopnosti, peníze, vybavení a poznámky. Tato aplikace takové deníky ukládá, zobrazuje a umožňuje je upravovat.</p>
                </section>
            </div>

            <aside class="col-12 col-lg-5" aria-label="Pravidla a rozcestník">
                <section class="home__card home__card--pdf" aria-labelledby="pravidla">
                    <h2 id="pravidla">Pravidla ke stažení</h2>
                    <p>Přehled ras a základních povolání ze hry Dračí doupě II ve formátu PDF.</p>
                    <a class="btn btn-dark" href="<?= $link->asset('pdf/DraciDoupeII.pdf') ?>" target="_blank" rel="noopener">Otevřít pravidla (PDF)</a>
                </section>

                <section class="home__card" aria-labelledby="v-aplikaci">
                    <h2 id="v-aplikaci">V aplikaci najdeš</h2>
                    <ul class="home__links list-unstyled mb-0">
                        <li><a href="<?= $link->url('dennik.index') ?>"><strong>Deníky</strong></a> – přehled postav, detail, úpravy a mazání</li>
                        <li><a href="<?= $link->url('dennik.create') ?>"><strong>Nový deník</strong></a> – vytvoření postavy</li>
                        <li><a href="<?= $link->url('rasa.index') ?>"><strong>Rasa</strong></a> – rasy a jejich rasové schopnosti</li>
                        <li><a href="<?= $link->url('povolanie.index') ?>"><strong>Povolání</strong></a> – povolání a jejich zvláštní schopnosti</li>
                    </ul>
                </section>
            </aside>
        </div>
    </div>
</main>
