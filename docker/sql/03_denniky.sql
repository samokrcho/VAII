-- =============================================================================
-- Ukážkové denníky postáv (vlastník: používateľ admin) – aby zoznam denníkov nebol na začiatku prázdny.
-- Spúšťa sa po 02_data.sql. Súčet úrovní povolaní jedného denníka je najviac 6.
-- =============================================================================

SET NAMES utf8mb4;

SET @admin = (SELECT id_pouzivatel FROM pouzivatel WHERE meno = 'admin' LIMIT 1);

-- Thorgrim Kamenná pěst (Trpaslíci, Bojovník 3, Lovec 2, Kejklíř 1)
INSERT INTO dennik (id_pouzivatel, id_rasa, id_rasova_schopnost, meno_postavy, popis, je_verejny, telo, dusa, vplyv, ohrozenie, vyhoda, dukaty, grose, haliere, vybavenie)
SELECT @admin, r.id_rasa, rs.id_rasova_schopnost, 'Thorgrim Kamenná pěst', 'Veterán z hlubinných dolů. Po zániku rodné pevnosti se živí jako žoldák a v poslední době se učí střílet z luku a házet noži.', 1, 8, 3, 4, 2, 1, 35, 12, 4, 'Sekera obouruční | | L
Kroužková košile | | T
Štít | | S
Lano | 15 m | S
Ploché pivo | | L'
FROM rasa r JOIN rasova_schopnost rs ON rs.id_rasa = r.id_rasa WHERE r.nazov = 'Trpaslíci' AND rs.nazov = 'Nezdolnost';
SET @d = LAST_INSERT_ID();
INSERT INTO dennik_povolanie (id_dennik, id_povolanie, uroven) SELECT @d, id_povolanie, 3 FROM povolanie WHERE nazov = 'Bojovník';
INSERT INTO dennik_povolanie (id_dennik, id_povolanie, uroven) SELECT @d, id_povolanie, 2 FROM povolanie WHERE nazov = 'Lovec';
INSERT INTO dennik_povolanie (id_dennik, id_povolanie, uroven) SELECT @d, id_povolanie, 1 FROM povolanie WHERE nazov = 'Kejklíř';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 1 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Bojovník' AND zs.nazov = 'Cit pro zbraň';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 2 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Bojovník' AND zs.nazov = 'Silák';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 3 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Bojovník' AND zs.nazov = 'Pádný úder';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 1 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Lovec' AND zs.nazov = 'Dobrá muška';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 2 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Lovec' AND zs.nazov = 'Průzkumník';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 1 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Kejklíř' AND zs.nazov = 'Děsivá přesnost';

-- Elwë Stříbrný list (Elfové, Zaříkávač 3, Lovec 1)
INSERT INTO dennik (id_pouzivatel, id_rasa, id_rasova_schopnost, meno_postavy, popis, je_verejny, telo, dusa, vplyv, ohrozenie, vyhoda, dukaty, grose, haliere, vybavenie)
SELECT @admin, r.id_rasa, rs.id_rasova_schopnost, 'Elwë Stříbrný list', 'Mladý elf z Domu pod pahorky, který opustil rodinu kvůli staré knize. Hledá prameny magie po celém kraji.', 1, 3, 9, 5, 0, 2, 20, 3, 0, 'Hůl ze stříbrného dřeva | | L
Kniha zaklínadel | | S
Lahvička inkoustu | | L
Plášť barvy mlhy | | T'
FROM rasa r JOIN rasova_schopnost rs ON rs.id_rasa = r.id_rasa WHERE r.nazov = 'Elfové' AND rs.nazov = 'Plášť soumraku';
SET @d = LAST_INSERT_ID();
INSERT INTO dennik_povolanie (id_dennik, id_povolanie, uroven) SELECT @d, id_povolanie, 3 FROM povolanie WHERE nazov = 'Zaříkávač';
INSERT INTO dennik_povolanie (id_dennik, id_povolanie, uroven) SELECT @d, id_povolanie, 1 FROM povolanie WHERE nazov = 'Lovec';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 1 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Zaříkávač' AND zs.nazov = 'Knihomol';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 2 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Zaříkávač' AND zs.nazov = 'Síla osobnosti';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 3 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Zaříkávač' AND zs.nazov = 'Bystrost';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 1 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Lovec' AND zs.nazov = 'Dobrá muška';

-- Bran z Rokle (Lidé, Kejklíř 2, Mastičkář 1)
INSERT INTO dennik (id_pouzivatel, id_rasa, id_rasova_schopnost, meno_postavy, popis, je_verejny, telo, dusa, vplyv, ohrozenie, vyhoda, dukaty, grose, haliere, vybavenie)
SELECT @admin, r.id_rasa, rs.id_rasova_schopnost, 'Bran z Rokle', 'Pouliční kejklíř z přístavního města. Zná půlku podsvětí a druhá půlka zná jeho.', 1, 6, 4, 8, 1, 0, 60, 20, 11, 'Dýky na vrhání | 5 ks | L
Lékárnička | | L
Kostky a karty | | L
Šedý plášť | | T'
FROM rasa r JOIN rasova_schopnost rs ON rs.id_rasa = r.id_rasa WHERE r.nazov = 'Lidé' AND rs.nazov = 'Styky';
SET @d = LAST_INSERT_ID();
INSERT INTO dennik_povolanie (id_dennik, id_povolanie, uroven) SELECT @d, id_povolanie, 2 FROM povolanie WHERE nazov = 'Kejklíř';
INSERT INTO dennik_povolanie (id_dennik, id_povolanie, uroven) SELECT @d, id_povolanie, 1 FROM povolanie WHERE nazov = 'Mastičkář';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 1 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Kejklíř' AND zs.nazov = 'Akrobat';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 2 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Kejklíř' AND zs.nazov = 'Zloděj';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 1 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Mastičkář' AND zs.nazov = 'Vyjednavač';

-- Bruna Dobrosrdcová (Hobiti, Mastičkář 4)
INSERT INTO dennik (id_pouzivatel, id_rasa, id_rasova_schopnost, meno_postavy, popis, je_verejny, telo, dusa, vplyv, ohrozenie, vyhoda, dukaty, grose, haliere, vybavenie)
SELECT @admin, r.id_rasa, rs.id_rasova_schopnost, 'Bruna Dobrosrdcová', 'Hobití bylinkářka, která léčí celou vesnici. Nejraději vaří, ale když je třeba, umí se rozzlobit.', 1, 4, 8, 9, 0, 3, 15, 40, 25, 'Brašna s bylinami | | S
Sada nožíků a jehel | | L
Hrnec na vaření | | S
Koláče na cestu | 3 ks | L'
FROM rasa r JOIN rasova_schopnost rs ON rs.id_rasa = r.id_rasa WHERE r.nazov = 'Hobiti' AND rs.nazov = 'Dobrá nálada';
SET @d = LAST_INSERT_ID();
INSERT INTO dennik_povolanie (id_dennik, id_povolanie, uroven) SELECT @d, id_povolanie, 4 FROM povolanie WHERE nazov = 'Mastičkář';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 1 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Mastičkář' AND zs.nazov = 'Ranhojič';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 2 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Mastičkář' AND zs.nazov = 'Čtení v duši';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 3 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Mastičkář' AND zs.nazov = 'Hojivý dotek';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 4 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Mastičkář' AND zs.nazov = 'Moudrý rádce';

-- Rurik Černý štít (Lidé, Bojovník 1)
INSERT INTO dennik (id_pouzivatel, id_rasa, id_rasova_schopnost, meno_postavy, popis, je_verejny, telo, dusa, vplyv, ohrozenie, vyhoda, dukaty, grose, haliere, vybavenie)
SELECT @admin, r.id_rasa, rs.id_rasova_schopnost, 'Rurik Černý štít', 'Mladý panoš, který právě začíná. Sní o tom, že jednou povede vlastní družinu.', 0, 7, 2, 3, 0, 0, 8, 15, 0, 'Meč | | L
Dřevěný štít | | S
Kožená zbroj | | T'
FROM rasa r JOIN rasova_schopnost rs ON rs.id_rasa = r.id_rasa WHERE r.nazov = 'Lidé' AND rs.nazov = 'Lví srdce';
SET @d = LAST_INSERT_ID();
INSERT INTO dennik_povolanie (id_dennik, id_povolanie, uroven) SELECT @d, id_povolanie, 1 FROM povolanie WHERE nazov = 'Bojovník';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 1 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Bojovník' AND zs.nazov = 'Cit pro zbraň';

-- Garrok Zubří kůže (Krollové, Bojovník 4, Lovec 2)
INSERT INTO dennik (id_pouzivatel, id_rasa, id_rasova_schopnost, meno_postavy, popis, je_verejny, telo, dusa, vplyv, ohrozenie, vyhoda, dukaty, grose, haliere, vybavenie)
SELECT @admin, r.id_rasa, rs.id_rasova_schopnost, 'Garrok Zubří kůže', 'Kroll z hraničních hvozdů. Hrubý na pohled, ale ve stopách zvěře i lidí se vyzná jako málokdo.', 1, 10, 2, 2, 3, 1, 5, 0, 8, 'Okovaný kyj | | T
Oštěp | 2 ks | S
Kožešiny | | S'
FROM rasa r JOIN rasova_schopnost rs ON rs.id_rasa = r.id_rasa WHERE r.nazov = 'Krollové' AND rs.nazov = 'Zubří kůže';
SET @d = LAST_INSERT_ID();
INSERT INTO dennik_povolanie (id_dennik, id_povolanie, uroven) SELECT @d, id_povolanie, 4 FROM povolanie WHERE nazov = 'Bojovník';
INSERT INTO dennik_povolanie (id_dennik, id_povolanie, uroven) SELECT @d, id_povolanie, 2 FROM povolanie WHERE nazov = 'Lovec';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 1 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Bojovník' AND zs.nazov = 'Silák';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 2 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Bojovník' AND zs.nazov = 'Pádný úder';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 3 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Bojovník' AND zs.nazov = 'Hlava nehlava';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 4 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Bojovník' AND zs.nazov = 'Osobní strážce';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 1 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Lovec' AND zs.nazov = 'Průzkumník';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 2 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Lovec' AND zs.nazov = 'Zálesák';

-- Pip Třesoruký (Hobiti, Kejklíř 3)
INSERT INTO dennik (id_pouzivatel, id_rasa, id_rasova_schopnost, meno_postavy, popis, je_verejny, telo, dusa, vplyv, ohrozenie, vyhoda, dukaty, grose, haliere, vybavenie)
SELECT @admin, r.id_rasa, rs.id_rasova_schopnost, 'Pip Třesoruký', 'Hobit, který si v sobě nese víc, než by se zdálo, a který pod prsty cítí, co mu nepatří. Zatím ho nikdo nechytil.', 1, 3, 7, 6, 1, 2, 45, 9, 2, 'Prak a kapsa kamenů | | L
Sada paklíčů | | L
Skrytý váček | | L'
FROM rasa r JOIN rasova_schopnost rs ON rs.id_rasa = r.id_rasa WHERE r.nazov = 'Hobiti' AND rs.nazov = 'Tichošlápek';
SET @d = LAST_INSERT_ID();
INSERT INTO dennik_povolanie (id_dennik, id_povolanie, uroven) SELECT @d, id_povolanie, 3 FROM povolanie WHERE nazov = 'Kejklíř';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 1 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Kejklíř' AND zs.nazov = 'Skrytá kapsa';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 2 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Kejklíř' AND zs.nazov = 'Špeh';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 3 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Kejklíř' AND zs.nazov = 'Komediant';

-- Sylvara Měsíční (Elfové, Zaříkávač 4, Mastičkář 2)
INSERT INTO dennik (id_pouzivatel, id_rasa, id_rasova_schopnost, meno_postavy, popis, je_verejny, telo, dusa, vplyv, ohrozenie, vyhoda, dukaty, grose, haliere, vybavenie)
SELECT @admin, r.id_rasa, rs.id_rasova_schopnost, 'Sylvara Měsíční', 'Starší elfka, kněžka zapomenutého kultu. Ráda by se vrátila k Domu, ale nejdřív musí dokončit, co začala.', 1, 4, 11, 8, 2, 3, 70, 0, 0, 'Rituální hůl | | L
Stříbrný přívěšek | | L
Zaklínací obrazce | 4 ks | L
Léčivé masti | | L'
FROM rasa r JOIN rasova_schopnost rs ON rs.id_rasa = r.id_rasa WHERE r.nazov = 'Elfové' AND rs.nazov = 'Tesknota';
SET @d = LAST_INSERT_ID();
INSERT INTO dennik_povolanie (id_dennik, id_povolanie, uroven) SELECT @d, id_povolanie, 4 FROM povolanie WHERE nazov = 'Zaříkávač';
INSERT INTO dennik_povolanie (id_dennik, id_povolanie, uroven) SELECT @d, id_povolanie, 2 FROM povolanie WHERE nazov = 'Mastičkář';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 1 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Zaříkávač' AND zs.nazov = 'Knihomol';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 2 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Zaříkávač' AND zs.nazov = 'Síla osobnosti';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 3 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Zaříkávač' AND zs.nazov = 'Zřící stráže';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 4 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Zaříkávač' AND zs.nazov = 'Povolávač';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 1 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Mastičkář' AND zs.nazov = 'Ranhojič';
INSERT INTO dennik_zvlastna_schopnost (id_dennik, id_povolanie, id_schopnost, uroven_ziskania) SELECT @d, p.id_povolanie, zs.id_schopnost, 2 FROM povolanie p JOIN zvlastna_schopnost zs ON zs.id_povolanie = p.id_povolanie WHERE p.nazov = 'Mastičkář' AND zs.nazov = 'Čtení v duši';

-- Maximá vlastností (zdatnosť): aktuálna hodnota + 2, najviac 10.
UPDATE dennik SET telo_max = LEAST(10, telo + 2), dusa_max = LEAST(10, dusa + 2), vplyv_max = LEAST(10, vplyv + 2) WHERE id_pouzivatel = @admin;
