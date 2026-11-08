-- =============================================================================
-- Dáta z pravidiel Dračí Doupě II (rasy, rasové schopnosti, povolania, zvláštne schopnosti).
-- Spúšťa sa po 01_schema.sql.
-- Schopnosť: vlastnost (telo/duse/vliv), citat a text pravidla (aktivácia je na konci textu v zátvorke).
-- Popis povolania obsahuje jeho dovednosti (bloky oddelené prázdnym riadkom).
-- =============================================================================

SET NAMES utf8mb4;

-- Skúšobný používateľ (heslo: heslo123) – len pre vývoj, pred nasadením zmažte.
INSERT INTO pouzivatel (meno, email, heslo_hash, rola) VALUES ('admin', 'admin@example.com', '$2y$10$A9UnhVl5fgHOR3gP5u5PNOhbZ3jLgTIKaz5nd2im7KZsJcDaMRE5W', 'admin');

INSERT INTO rasa (nazov, popis) VALUES ('Lidé', 'Různorodí a odvážní hrdinové, hltající život plnými doušky.');
SET @id_rasa = LAST_INSERT_ID();
INSERT INTO rasova_schopnost (id_rasa, nazov, vlastnost, citat, text) VALUES
  (@id_rasa, 'Jménem krále', 'duse', 'Oddanost ušlechtilé věci mu dodává sílu vytrvat až do konce.', 'Zasadí-li si hrdina Jizvu při prosazování zájmů pána nebo organizace, kterým dlouhodobě slouží, získá o 1 Zdroj navíc.'),
  (@id_rasa, 'Zarputilost', 'vliv', 'Mohou mu vyčítat, že je bezohledný, sobecký nebo paličatý, ale on to nazývá odvahou stát si za svým.', 'Zasadí-li si hrdina Jizvu na Vlivu, získá vždy 1 Vliv navíc.'),
  (@id_rasa, 'Idol žen či mužů', 'vliv', 'Jeho divoký vzhled či naopak jemný šarm jsou zbraní, která láme i ta nejnepřístupnější srdce.', 'Postava má nadání (2 manévry) pro ovlivňování osob opačného pohlaví.'),
  (@id_rasa, 'Lví srdce', 'vliv', 'Je statečný a neotřese jím sebevětší nebezpečí či beznadějná situace.', 'Postava má přirozenou ochranu (sleva 1 na vyčerpání), pokud čelí zastrašování či psychickému nátlaku.'),
  (@id_rasa, 'Styky', 'vliv', 'Díky svému postavení nebo cestování má spoustu kontaktů na nejrůznějších místech.', 'S touto obsahovou pravomocí může hráč stanovit, že určitá cizí postava dluží jeho postavě z minulosti laskavost, nebo že s ní má společného známého, ke kterému mají oba kladný vztah. (Aktivace 1 Vliv)');

INSERT INTO rasa (nazov, popis) VALUES ('Elfové', 'Starý lid, naplněný moudrostí věků a obdařený nestárnoucí krásou.');
SET @id_rasa = LAST_INSERT_ID();
INSERT INTO rasova_schopnost (id_rasa, nazov, vlastnost, citat, text) VALUES
  (@id_rasa, 'Vznešenost', 'vliv', 'Jeho osobní charisma rychle zastíní případný tik.', 'Při uzdravování svých Jizev na Vlivu stačí postavě vynaložit 4 Zdroje namísto obvyklých 5.'),
  (@id_rasa, 'Tesknota', 'duse', 'Jeho mysl je bezedná studnice moudrosti a vědomostí, s nimi však vyplouvají na povrch i bolestné vzpomínky.', 'Zasadí-li si hrdina Jizvu na Duši, získá vždy 1 Duši navíc.'),
  (@id_rasa, 'Jsme jedné krve', 'vliv', 'Zvířata k němu cítí až zázračnou sounáležitost.', 'Postava má nadání (2 manévry) pro Ovlivňování zvířat.'),
  (@id_rasa, 'Plášť soumraku', 'duse', 'Naučil se procházet nocí neviděn, splynout se stíny a skrýt se zrakům ostatních.', 'Postava má nadání (2 manévry) pro skrývání se ve tmě, v šeru nebo při špatném osvětlení.'),
  (@id_rasa, 'Paměť rodu', 'duse', 'O každém místě, stromě nebo kameni zná příběh, který se skutečně před lety odehrál.', 'S touto obsahovou pravomocí může hráč stanovit, že k místu či situaci se vztahuje nějaký dávný zvyk či obyčej, který postava využije. (Aktivace 1 Duše)');

INSERT INTO rasa (nazov, popis) VALUES ('Trpaslíci', 'Nezdolní a hrdí dobrodruhové, pro které je čest a dané slovo vším.');
SET @id_rasa = LAST_INSERT_ID();
INSERT INTO rasova_schopnost (id_rasa, nazov, vlastnost, citat, text) VALUES
  (@id_rasa, 'Noční oči', 'duse', 'I v nejčernější tmě vidí jako jiní v šeru. Šero je pro něj jasným dnem.', 'Postava s tímto neobvyklým smyslem dokáže vidět ve tmě.'),
  (@id_rasa, 'Síla přísahy', 'duse', 'Jednou dané slovo je pro trpaslíky nezrušitelné a posvátné.', 'Zasadí-li si hrdina Jizvy při plnění přísahy, získává o 1 Zdroj navíc.'),
  (@id_rasa, 'Nezdolnost', 'telo', 'Co jiní vnímají jako vážné zranění, to je pro něho jenom škrábnutí.', 'Při uzdravování svých Jizev na Těle stačí postavě vynaložit 4 Zdroje namísto obvyklých 5.'),
  (@id_rasa, 'Játra ze žuly', 'telo', 'Nikdy se nepouštějte do pijáckého duelu s trpaslíkem.', 'Postava má přirozenou ochranu (slevu 1 na vyčerpání) proti účinkům jedů.'),
  (@id_rasa, 'Pouto krve', 'vliv', 'Pochází z rozvětveného rodu, takže má řadu bratranců i přívrženců.', 'S touto obsahovou pravomocí může hráč stanovit, že určitý trpaslík patří k příbuzným jeho postavy nebo má k jejímu rodu závazky. (Aktivace 1 Vliv)');

INSERT INTO rasa (nazov, popis) VALUES ('Hobiti', 'Drobný národ, ve kterém dříme víc odvahy, než se na první pohled zdá.');
SET @id_rasa = LAST_INSERT_ID();
INSERT INTO rasova_schopnost (id_rasa, nazov, vlastnost, citat, text) VALUES
  (@id_rasa, 'Šestý smysl', 'duse', 'Když mu hrozí nějaká nepříjemnost, okamžitě pocítí neklid a zbystří.', 'Hráč může určit, že jeho postava, je-li bdělá, dokáže včas zareagovat na skryté nebezpečí, jako by měla alespoň podezření na jeho přítomnost. (Aktivace 1 Duše)'),
  (@id_rasa, 'Takový prcek!', 'telo', 'Je snadné jej podcenit, neboť bojuje zuřivěji, než by člověk od tak drobné rasy, jako jsou hobiti, čekal.', 'Když postava bojuje zblízka se soupeřem výrazně větším než je sama, získává do souboje 2 Výhody.'),
  (@id_rasa, 'Dobrá nálada', 'duse', 'Na všechen děs a útrapy rázem zapomene, když před něj někdo postaví žejdlík piva a rozestele mu čistou postel.', 'Při uzdravování svých Jizev na Duši stačí postavě vynaložit 4 Zdroje namísto obvyklých 5.'),
  (@id_rasa, 'Tichošlápek', 'duse', 'Díky měkkému došlapu dokáže proklouznout kamkoliv zcela neslyšně.', 'Postava má nadání (2 manévry) pro skrývání se, pokud při něm nemá na nohách boty a využije své lehké chůze.'),
  (@id_rasa, 'Labužník', 'duse', 'Vždycky má u sebe něco pro zlepšení nálady nebo navození pocitu domova.', 'S touto obsahovou pravomocí hráč může kdykoliv, kdy je to uvěřitelné, určit, že jeho postava má u sebe něco dobrého – placatku s vínem či kořalkou, hrst dýmkového tabáku nebo třeba pytlíček rozinek, sušených hub či exotického koření. (Aktivace 1 Duše)');

INSERT INTO rasa (nazov, popis) VALUES ('Krollové', 'Silní a odolní hrdinové hromotluckého vzhledu s odvážným srdcem.');
SET @id_rasa = LAST_INSERT_ID();
INSERT INTO rasova_schopnost (id_rasa, nazov, vlastnost, citat, text) VALUES
  (@id_rasa, 'Netopýří sluch', 'duse', 'Zaslechne i to nejslabší prasknutí větvičky, slova pronášená šeptem nebo tichý dech skrývajícího se tvora.', 'Postava s tímto neobvyklým smyslem dokáže zaslechnout i velmi tiché zvuky a orientovat se podle nich i ve tmě.'),
  (@id_rasa, 'Zběsilost', 'telo', 'Bolest a chuť vlastní krve na rtech ho dokážou vybičovat k neskutečným výkonům, nad kterými rozum zůstává stát.', 'Zasadí-li si hrdina Jizvu na Těle, získá vždy 1 Tělo navíc.'),
  (@id_rasa, 'Odznaky hrdinství', 'vliv', 'Bizarní ozdoby, tetování nebo účes ohlašují, že vykonal odvážný skutek, a dodávají mu sebevědomí.', 'Postava má nadání (2 manévry) pro zastrašování všech rozumných tvorů.'),
  (@id_rasa, 'Zubří kůže', 'telo', 'Tuhá srst jej chrání lépe než prošívaná zbroj.', 'Postava má přirozenou zbroj (slevu 1 na vyčerpání) proti tělesným zraněním.'),
  (@id_rasa, 'Kořeny magie', 'duse', 'Duchové předků vždy odpovědí na jeho zavolání a dovedou jej ke zdroji moci.', 'S touto obsahovou pravomocí může hráč určit a popsat vlastnost, okolí či chování přirozeného magického zřídla. (Aktivace 1 Duše)');

INSERT INTO povolanie (nazov, popis) VALUES ('Bojovník', 'Běžný boj zblízka (Tělo)
» boj zblízka se zbraní i beze zbraně proti běžným protivníkům (zejména lidem a zvířatům), útoky, finty, obrany, kryty
» využívání zbraní a zbrojí, rozbíjení věcí, dveří apod. zbraněmi, sekání lan, krytí se štítem před šípy
» triky se zbraněmi (například vyhazování a chytání zbraně, rozsekávání šátků ve vzduchu, chytání hozených věnců na kopí)

Síla (Tělo)
» hrubá síla, vyrážení dveří, zdvihání břemen, tlačení, tažení a držení silou

Bojovnické znalosti (Duše)
» bájesloví, znalost legend o slavných bitvách, zbraních a válečnících
» znalost výzbroje, odhad druhu, kvality, ceny a nemagických schopností zbraně či zbroje, povědomí o výrobních postupech
» bojová taktika, schopnost orientace, manévrování a zaujetí výhodného postavení v bitce, příprava, provedení či odhalení léčky, držení formace

Sebedůvěra (Vliv)
» chvástání, vychvalování svých činů a schopností, získávání úcty a obdivu lidí, oceňování a ostouzení, umění pochválit či pohanět cizí výzbroj či bojové schopnosti, zastrašování lidí, zlomení morálky, zahnání na útěk');
SET @id_povolanie = LAST_INSERT_ID();
INSERT INTO zvlastna_schopnost (id_povolanie, nazov, vlastnost, citat, text, minimalna_uroven) VALUES
  (@id_povolanie, 'Cit pro zbraň', 'telo', 'Ten chlap to s tesákem umí, a nemám na mysli jen porcování masa, jestli mi rozumíte.', 'Hráč si vybere jeden druh zbraně pro boj zblízka (např. „kopí“ nebo „meč“). Postava má nadání (2 manévry) pro pěší i jízdní boj s tímto druhem zbraně proti jakémukoliv protivníkovi i pro jiné používání tohoto druhu zbraně.', 0),
  (@id_povolanie, 'Gladiátor', 'telo', 'Vybojoval už tolik duelů s lidmi i zvířaty, že přesně ví, jak se jim dostat na kobylku.', 'Postava je zběhlá (základní manévry zdarma) v Běžném boji zblízka.', 0),
  (@id_povolanie, 'Silák', 'telo', 'Vyvrátí okované dveře z pantů a ani se při tom nezapotí.', 'Postava je zběhlá (základní manévry zdarma) v Síle.', 0),
  (@id_povolanie, 'Osobní strážce', 'telo', 'Svého zákazníka nebo přítele dostane živého a zdravého z jakéhokoliv nebezpečí.', 'Postava může tělesnými akcemi reagovat akcí z obrany nejen na akce vedené na sebe, jak je obvyklé, ale i na jakékoliv akce škodící jiné postavě nebo předmětu, které má v dosahu.', 0),
  (@id_povolanie, 'Kruhový sek', 'telo', 'Díky svému šermířskému umění dokáže naprášit celému oddílu.', 'Útočíli postava v boji zblízka plošně nejvýše na sevřenou hrstku (tucet) cílů, může použít manévr rozsáhle zdarma.', 0),
  (@id_povolanie, 'Pádný úder', 'telo', 'Ranou své zbraně prorazí i pevnou zbroj nebo rozdrtí přilbu.', 'Proti tělesným útokům postavy poskytují zbroje o 1 menší slevu na vyčerpání.', 0),
  (@id_povolanie, 'Hlava nehlava', 'telo', 'V zápalu boje rozbije soupeři o hlavu takřka cokoliv.', 'Postava může při boji zblízka zničit nouzovou zbraň, kterou používá, a získat tak 1 Zdroj, jako by udělila Jizvu zbrani běžné kvality.', 0),
  (@id_povolanie, 'Rameno na rameno', 'telo', 'Věrnému spolubojovníkovi dokáže pomoci v útoku i krýt záda.', 'Postava může v boji zblízka i mimo svou akci pomoci jiné postavě s její akcí (např. vykrýt ránu, pomoci se zvednout na nohy, zapřít), pokud se nachází dostatečné blízko, a poskytnout jí pro tuto akci svoje Tělo (na zaplacení aktivace, náročnosti, manévrů nebo vyčerpání).', 0),
  (@id_povolanie, 'Skald', 'duse', 'Zná příběhy o slavných hrdinech a bojovnících, velkých bitvách i prastarých artefaktech. A dokáže si z nich vzít poučení pro sebe.', 'Postava je zběhlá (základní manévry zdarma) v Bojovnických znalostech.', 0),
  (@id_povolanie, 'Skvělý taktik', 'duse', 'Jeho schopnost bleskově zhodnotit bojovou situaci a v okamžiku se jí přizpůsobit z něj činí obávaného protivníka.', 'Jeli postava přepadena, získává 1 Výhodu. Postava může, po dostatečně předchozí přípravě bojového plánu, používat v bitce zdarma přístupy k iniciativě, dokud jdou věci podle plánu.', 0),
  (@id_povolanie, 'Šampión', 'vliv', 'Jeho slova a chování dokážou zdrtit soupeře dřív, než začne samotný boj.', 'Postava je zběhlá (základní manévry zdarma) v Sebedůvěře.', 0),
  (@id_povolanie, 'Přečtení soupeře', 'vliv', 'Pozná, co může od svého protivníka čekat, a ví, jak toho využít, často ještě dřív, než se s ním střetne.', 'S touto obsahovou pravomocí může hráč stanovit, že budoucí soupeř jeho postavy má při boji zblízka určitou slabinu nebo nějakou oblíbenou taktiku, kterou ve střetu s jeho postavou využije. (Aktivace 1 Vliv)', 0);

INSERT INTO povolanie (nazov, popis) VALUES ('Lovec', 'Běžná střelba (Tělo)
» používání střelných zbraní od krátkého luku až po kuši proti běžným protivníkům (zejména lidem a zvířatům), střelba na terč, zručnost ve střelbě

Cestování (Tělo)
» pohyb v divočině v pralese, v džungli, bažině, sněhu, brodění
» pohyb ve vodě, plavání a potápění, řízení loděk a vorů, pádlování
» vytrvalost, dlouhé pochody

Orientace v divočině (Duše)
» znalost zákonitostí prostředí divočiny, odhad správného směru, vyhýbání se nebezpečným místům a jevům
» ukrývání a odhalování osob a předmětů v divočině, tichý pohyb v divočině, stopování a matení stop
» taktika boje z dálky v divočině, volba vhodného místa, vyčkávání a setrvání ve vybrané pozici

Tábornictví (Duše)
» zálesáctví, táboření, práce s ohněm, stavba stanů a přístřešků, vaření
» výroba primitivních nástrojů (například pochodní, šípů, pazourků a oštěpů, jednoduchého oblečení z kůže, kostěných šperků)
» hledání potravy v divočině (například hledání vody, sběr hub, plodů, bylin, drobných živočichů a vajec)
» výroba, líčení, nalézání a zneškodňování jednoduchých pastí (například smyčky, oka, padající stromy a kameny), odhad jejich vlastností a zamýšleného účinku, kladení návnad na zvířata, příprava tenat na ptáky, vrší na ryby

Lovecké znalosti (Duše)
» zpracování zvířecí kořisti, braní zvířecích trofejí, znalost trhů s masem, kožešinami a trofejemi a jejich cen
» znalost zvířat, rozeznávání nepřirozeného chování, odhad charakteristiky a hranic zvířat

Ovlivňování zvířat (Vliv)
» odhad pocitů a úmyslů zvířat, ovlivňování jejich pocitů (od laskavosti až po přísnost), vyvolání pocitů (od klidu až po paniku), získání si důvěry
» zastrašování zvířat, zlomení sebedůvěry, zahnání na útěk, získání respektu
» vábení, napodobování hlasů zvířat, přilákání, přivolání zvířat v místě či okolí se vyskytujících
» ochočení, krocení, cvičení zvířat k následování povelů
» velení zvířatům, ovládnutí ochočených zvířat prostřednictvím jednoduchých povelů, využívání zvířat ke stopování a jiným činnostem');
SET @id_povolanie = LAST_INSERT_ID();
INSERT INTO zvlastna_schopnost (id_povolanie, nazov, vlastnost, citat, text, minimalna_uroven) VALUES
  (@id_povolanie, 'Dobrá muška', 'telo', 'Se svou oblíbenou zbraní dokáže zasáhnout hned první ranou, jako by cíl jeho střelu přímo přitahoval.', 'Hráč si vybere jeden druh střelné zbraně (např. „luk“). Postava má nadání (2 manévry) pro střelbu tímto druhem zbraně na jakýkoliv cíl.', 0),
  (@id_povolanie, 'Ostrostřelec', 'telo', 'Před jeho střelami není úniku. Najde si svou kořist v jakémkoliv úkrytu a ví, jak šípem spolehlivě zmrzačit zvíře nebo člověka.', 'Postava je zběhlá (základní manévry zdarma) v Běžné střelbě.', 0),
  (@id_povolanie, 'Déšť střel', 'telo', 'První šíp ještě sviští vzduchem a zkušená ruka již zakládá na tětivu druhý.', 'Střílíli postava plošně nejvýše na sevřenou hrstku (tucet) cílů a má při tom dostatek dobře přístupné munice (například šípy zabodané před sebou v zemi), může použít manévr rozsáhle zdarma.', 0),
  (@id_povolanie, 'Zálesák', 'duse', 'V divočině je jako doma, vždycky ví, kde najít něco k snědku, a pozná, kde je vhodné složit na noc hlavu.', 'Postava je zběhlá (základní manévry zdarma) v Tábornictví.', 0),
  (@id_povolanie, 'Průzkumník', 'duse', 'V přírodním terénu se pohybuje tiše jako duch, vždycky najde správnou cestu a jeho ostrému zraku nic neunikne.', 'Postava je zběhlá (základní manévry zdarma) v Orientaci v divočině.', 0),
  (@id_povolanie, 'Průvodce', 'duse', 'Nejen že se v lese vyzná jako nikdo jiný, ale dokáže jím bezpečně provést i celou kupeckou karavanu.', 'Postava je zběhlá (základní manévry zdarma) v Cestování. Postava může zdarma použít manévr rozsáhle při Cestování nebo Orientaci v divočině na společně putující družinu.', 0),
  (@id_povolanie, 'Lovčí', 'duse', 'Zvířata svého revíru zná jak nikdo jiný a úlovek dokáže zpracovat do posledního pírka.', 'Postava je zběhlá (základní manévry zdarma) v Loveckých znalostech. Postava dokáže při odhadu zvířete určit i jeho zvláštní schopnosti. (Aktivace 1 Duše)', 0),
  (@id_povolanie, 'Něco z ničeho', 'duse', 'Nepotřebuje železa ani oka z drátů, k vytvoření smrtící léčky mu stačí dobře vybraný klacek a nůž.', 'Postava dokáže vyrobit základ jednoduché pasti z nouzových Surovin. (Aktivace 1 Duše) Postava dokáže při výrobě posilovat libovolné pasti s pomocí nouzových Surovin. Za každou takto vloženou Surovinu zaplatí 1 Duši.', 0),
  (@id_povolanie, 'Jedno oko otevřené', 'duse', 'Je ostražitý a vnímá, co se kolem děje, i když odpočívá nebo spí.', 'Hráč může určit, že jeho postava dokáže během nekouzelného spánku reagovat na nebezpečí ohrožující ji nebo jiného člena družiny, jako by byla vzhůru. (Aktivace 1 Duše)', 0),
  (@id_povolanie, 'Krotitel', 'vliv', 'I to nejvzpurnější zvíře zjihne pod jeho přísným pohledem či výhružným zamručením.', 'Postava je zběhlá (základní manévry zdarma) v Ovlivňování zvířat.', 0),
  (@id_povolanie, 'Cvičitel', 'vliv', 'Dejte mu pár dní a naučí panáčkovat i krokodýla.', 'Hráč může popsat, proč zvířecí pomocník jeho hrdiny má obsah své charakteristiky rozšířen o jednu dovednost, nebo má o jednu zvláštní schopnost více, než je pro obdobné bytosti obvyklé. Postava může s pomocí dovednosti Ovlivňování zvířat dočasně poskytnout stejné zlepšení zvířatům, která nejsou jejími pomocníky, po dobu, kdy následují její velení. Postava musí danou dovednost nebo zvláštní schopnost sama ovládat.', 0),
  (@id_povolanie, 'Zaříkávač koní', 'vliv', 'Při průchodu vesnicí na něj nezaštěká jediný pes, ale jeho kůň pokope kohokoli, kdo na něj bez dovolení sáhne.', 'S touto obsahovou pravomocí může hráč stanovit, jakou povahu či náladu má nějaké neočarované zvíře. (Aktivace 1 Vliv)', 0);

INSERT INTO povolanie (nazov, popis) VALUES ('Kejklíř', 'Běžný vrh (Tělo)
» vrhání malými předměty a zbraněmi (od nože až po oštěp) a metání kamenů prakem proti běžným protivníkům (zejména lidem a zvířatům), zručnost s vrhacími zbraněmi, vrh na terč, žonglování

Pohyblivost (Tělo)
» akrobacie, běh, sprint, mrštnost, skoky a pády, úhyby, šplh a lezení, rovnováha, protahování se úzkými otvory, pohyb ve městě, jízda na koni či voze, držení se v sedle

Triky a kejkle (Duše)
» triky s drobnými předměty, falešná hra
» prošacování, ukrývání a odhalování věcí na těle nebo v oděvu, kapsářství
» práce s provazy, spoutání, vykroucení se z pout, znalost uzlů, výroba provazových překážek, mostů, žebříků

Orientace ve městě (Duše)
» znalost zákonitostí prostředí města, odhad správného směru, vyhýbání se nebezpečným místům a jevům
» ukrývání či odhalování osobu nebo předmětů ve městě, nenápadnost, zmizení v davu, tichý pohyb ve městě, plížení, sledování
» taktika boje z dálky ve městě, volba vhodného místa, vyčkávání a setrvání ve vybrané pozici

Charisma (Vliv)
» svádění, vyvolávání soucitu či dojetí, radosti a smíchu, smutku, lásky či nenávisti u lidí
» využívání lidské povahy, přilákání, přivolání lidí v místě či okolí se vyskytujících

Převleky a předstírání (Vliv)
» ukrývání vlastní totožnosti, převleky, přestrojování se, líčení a masky, napodobování cizího hlasu a mimiky

Umění (Vliv)
» hra na hudební nástroje, zpěv, přednes, tanec, herectví, verše, malování, sochařství, pobavení či citové pohnutí publika');
SET @id_povolanie = LAST_INSERT_ID();
INSERT INTO zvlastna_schopnost (id_povolanie, nazov, vlastnost, citat, text, minimalna_uroven) VALUES
  (@id_povolanie, 'Děsivá přesnost', 'telo', 'Pokud se jím vržená zbraň chvěje v trámu vedle vaší hlavy, buďte si jistí, že neminul. Bylo to varování.', 'Hráč si vybere jeden druh vrhací zbraně (např. „nůž“ nebo „oštěp“). Postava má nadání (2 manévry) pro vrhání tímto druhem zbraně na jakýkoliv cíl.', 0),
  (@id_povolanie, 'Akrobat', 'telo', 'Je skvělý jezdec i běžec a má-li kam uskočit, přelézt nebo kudy se protáhnout, pronásledovatelé jen těžko dokážou udržet krok.', 'Postava je zběhlá (základní manévry zdarma) v Pohyblivosti.', 0),
  (@id_povolanie, 'Vrhač', 'telo', 'Zabíjel na dálku už tolikrát, že předvídá každý pohyb i úmysl své kořisti.', 'Postava je zběhlá (základní manévry zdarma) v Běžném vrhu.', 0),
  (@id_povolanie, 'Plášť', 'telo', 'Oděv dobře zvolený k dané příležitosti může být zbraní. V jeho podání doslova.', 'Když postava bojuje, může jednou svou rukou používat pláště, ubrusy, závěsy, deky a podobně, jako by to byl štít.', 0),
  (@id_povolanie, 'Smršť dýk', 'telo', '„A pak se roztrhlo nebe a začalo pršet kamení.“', 'Vrháli postava plošně nejvýše na sevřenou hrstku (tucet) cílů a má při tom dostatek dobře přístupné munice (například vrhací nože v pouzdrech na předloktích nebo připravené kameny na zemi před sebou), může použít manévr rozsáhle zdarma.', 0),
  (@id_povolanie, 'Zloděj', 'duse', 'Vyhodit kotvičku na hradební zeď je pro něj stejně snadné jako odříznout něčí váček nebo vytáhnout eso z rukávu.', 'Postava je zběhlá (základní manévry zdarma) v Tricích a kejklích,', 0),
  (@id_povolanie, 'Špeh', 'duse', 'Přesně ví, kde je možné se ve městě či na vsi schovat a kde naopak hledat to, co mělo uniknout jeho zvědavému zraku.', 'Postava je zběhlá (základní manévry zdarma) v Orientaci ve městě.', 0),
  (@id_povolanie, 'Talent', 'vliv', 'Když hrábne do strun, čas se zastaví a nejedno srdce pookřeje.', 'Hráč si vybere jeden druh hudebního nástroje nebo jiné umělecké pomůcky (např. „maska“, „taneční úbor“). Postava má nadání (2 manévry) pro Umění s použitím tohoto druhu pomůcky.', 0),
  (@id_povolanie, 'Psí oči', 'vliv', 'Když chce, je ztělesněním nevinnosti a bezbrannosti, čehož umí řádně využít.', 'Postava je zběhlá (základní manévry zdarma) v Charismatu a Převlecích a předstírání.', 0),
  (@id_povolanie, 'Komediant', 'vliv', 'Když vystupuje, může mu za zády přecházet karavana velbloudů a nikdo si jí ani nevšimne.', 'Postava je zběhlá (základní manévry zdarma) v Umění. Snažíli se postava plošně získat pozornost okolí pomocí Umění, může použít manévr rozsáhle zdarma.', 0),
  (@id_povolanie, 'Změna tváře', 'vliv', 'Nepotřebuje převleky ani líčidla. Prostě jen svraští obličej, rozcuchá si vlasy, shrbí se nebo napodobí záškuby šílence tak dovedně, že jej ani přátelé nepoznají.', 'Postava se s použitím Převleků a předstírání dokáže vydávat za někoho jiného (například za stařenu nebo žebráka), ačkoliv neměla dost času nebo prostředky k tomu, aby se nalíčila nebo použila převlek. Nemůže se však tímto způsobem vydávat za nějakou určitou existující osobu. (Aktivace 1 Vliv)', 0),
  (@id_povolanie, 'Skrytá kapsa', 'telo', 'Nikdo neví, jak to dělá, ale nějakou tu drobnost je schopen pronést kamkoliv, i kdyby ho u vchodu šacovali.', 'S touto obsahovou pravomocí může hráč určit, že jeho postava má u sebe jeden malý předmět ze svého vybavení do velikosti dlaně, a to i poté, co byla prohledána. (Aktivace 1 Tělo)', 0);

INSERT INTO povolanie (nazov, popis) VALUES ('Mastičkář', 'Odolnost a výdrž (Tělo)
» odolávání strastem, zimě, žáru, hladu, žízni, nemocem, otravám, popáleninám, poleptání, bolesti, alkoholu, jedům a omamným látkám

Ošetřování lidského těla (Duše)
» nacházení příčin a projevů nemocí, postřehnutí známek působení jedů a omamných látek, ohledávání mrtvol
» léčení lidského těla, zranění, nemocí a otrav, napravování kostí, oživovací zákroky, péče o nemocné, podávání mastí, znalost dávkování a vedlejších účinků
» mučení, vypalování cejchů, udržování při životě či vědomí
» lazebnictví, trhání zubů, stříhání a holení, tetování, braní lidských trofejí, sběr skalpů, uší apod., výroba paruk

Masti a běžné jedy (Duše)
» mastičkářské znalosti, obecný odhad všech lektvarů a jedů, odhad vlastností a účinků běžného jedu či masti podle zbarvení, čichem nebo chutí, zjišťování nebo maskování přítomnosti libolného jedu v jídle či nápoji
» obecná znalost kovů a kamenů, bylinkářství, tělesných šťáv a částí těl, výroba mastí, léků, dlah a přípravků k otupení bolesti, pomáhajících s uzdravováním těla lidí a zvířat
» výroba a podávání běžných jedů působících na lidi a zvířata
» výroba a podávání běžných protijedů, látek potlačujících účinky otravy běžným jedem

Znalost lidí (Duše)
» odhad vlastností a dovedností běžných lidí, znalost lidských druhů, ras, typů a povah, znalost chování, obecný odhad neobvyklých lidí

Vyjednávání s lidmi (Vliv)
» vyjednávání s lidmi, odhad motivací a názorů, přesvědčování, argumentace, slovíčkaření, přímé ovlivňování názorů, křížové výslechy');
SET @id_povolanie = LAST_INSERT_ID();
INSERT INTO zvlastna_schopnost (id_povolanie, nazov, vlastnost, citat, text, minimalna_uroven) VALUES
  (@id_povolanie, 'Dryjáčník', 'duse', 'Jedy, které vyrábí, porážejí protivníky spolehlivěji než ostrá dýka, uspávadla uspávají rychleji než rána do zátylku.', 'Postava je zběhlá (základní manévry zdarma) v Mastech a běžných jedech.', 0),
  (@id_povolanie, 'Ranhojič', 'duse', 'Kde běžný felčar dokáže tak nanejvýš pustit žilou pacientovi i jeho měšci, on zachraňuje životy.', 'Postava je zběhlá (základní manévry zdarma) v Ošetřování lidského těla.', 0),
  (@id_povolanie, 'Čtení v duši', 'duse', 'Vidí člověku až do žaludku a dokáže odhadnout jeho pravou povahu i schopnosti.', 'Postava je zběhlá (základní manévry zdarma) ve Znalosti lidí. Postava dokáže společně s odhadem určitého člověka zároveň zjistit i jeho zvláštní schopnosti. (Aktivace 1 Duše)', 0),
  (@id_povolanie, 'Umění improvizace', 'duse', 'Zatímco jiní zkoušejí chybějící přísadu nějak nahradit, on si zkrátka upraví celý recept.', 'Postava dokáže vyrobit základ běžného jedu s použitím nouzových Surovin. (Aktivace 1 Duše) Postava dokáže při výrobě posilovat libovolné jedy a protijedy s pomocí nouzových Surovin. Za kaž dou takto vloženou Surovin zaplatí 1 Duši.', 0),
  (@id_povolanie, 'Neobvyklé jedy', 'duse', 'Míchá jedy, jako by tvořil umělecké dílo – tu něco přidá, tam něco změní a výsledek je ohromující.', 'Postava dokáže vyrobit posílený rychlý jed, který se spustí okamžitě. (Aktivace 1 Surovina) Postava také dokáže vyrobit posílený dvousložkový jed, který zůstane v těle oběti a spustí se teprve poté, co oběť pozře jiný pokrm či nápoj. Nespuštěný jed zůstane v těle velmi dlouho (Aktivace 1 Surovina) Postava dokáže vyrobit posílený velmi dlouho působící jed. Musí přitom určit nějakou přirozenou činnost (pobyt na slunci, koupání ve studené vodě, dieta, ...), která účinky jedu potlačí. (Aktivace 3 Suroviny)', 0),
  (@id_povolanie, 'Vyjednavač', 'vliv', 'Ví, co a kdy je třeba říci, aby se rozhovor ubíral tím správným směrem.', 'Postava je zběhlá (základní manévry zdarma) ve Vyjednávání s lidmi.', 0),
  (@id_povolanie, 'Působivý řečník', 'vliv', 'Když promluví, všichni u stolu ztichnou...', 'Jednáli postava plošně nejvýše se sevřenou hrstkou (tuctem) cílů, může použít manévr rozsáhle zdarma.', 0),
  (@id_povolanie, 'Kluzký jako úhoř', 'vliv', '„Pěkně jeden po druhém, pánové!“', 'Pokud postava v kole jedná s více protivníky, může jedenkrát za kolo zdarma použít manévr obrana.', 0),
  (@id_povolanie, 'Ostrý jazyk', 'vliv', 'Umí se zastat svých přátel a jeho slova tnou hlouběji než meč.', 'Postava může akcemi spadajícími pod vliv reagovat pomocí akce z obrany nejen na akce vedené na sebe, jak je obvyklé, ale i na jakékoliv akce škodící jiné postavě, která se nachází v doslechu.', 0),
  (@id_povolanie, 'Moudrý rádce', 'vliv', 'Pouhá jeho přítomnost může jeho přátelům pomoci odolat nátlaku nebo přesvědčit o své pravdě odpůrce.', 'Postava může při jednání i mimo svou akci pomoci jiné postavě s její akcí (např. napovědět, povzbudit, odlákat pozornost), pokud se nachází v  doslechu, a poskytnout jí pro tuto akci svůj Vliv (na zaplacení aktivace, náročnosti, manévrů nebo vyčerpání).', 0),
  (@id_povolanie, 'Hojivý dotek', 'vliv', 'Už pouhá jeho přítomnost u lůžka zraněného či nemocného dodává naději i víru v uzdravení.', 'Jizvy na Těle dokáže léčit, jako by síla magického okamžiku byla o 1 vyšší. (Aktivace 1 Duše)', 0),
  (@id_povolanie, 'Lazebník', 'vliv', 'Holení a stříhání vlasů jsou jen záminkou k tomu si poklábosit a vyměnit novinky.', 'S touto obsahovou pravomocí může hráč stanovit, že o určité postavě koluje mezi lidmi nějaká fáma nebo pomluva. (Aktivace 1 Vliv)', 0);

INSERT INTO povolanie (nazov, popis) VALUES ('Zaříkávač', 'Učenost (Duše)
» vzdělání, čtení a psaní, cizí jazyky, znalost starých jazyků a písem, cestovatelské znalosti, čtení map a místopis, historie a pověsti, pověry a přísloví, hádanky a hry

Znalost nadpřirozena (Duše)
» okultní a náboženské vědomosti, orientace v různých náboženstvích a systémech magie, obecná znalost kouzelnických rituálů a prastarých jazyků, náboženských symbolů, obřadů a obětin, obecná znalost ochranných obrazců, rozpoznání nadpřirozena, nacházení magických zřídel a svatyní a míst výskytu nadpřirozených sil, obecná znalost nadpřirozených bytostí a odhad jejich síly, zkazky o jejich zvycích a chování
» výroba rituálních předmětů (např. přívěšek náboženského řádu, kouzelnická hůl), vkládání magické síly do nich, lámání rituálních předmětů

Ošetřování lidské duše (Duše)
» ošetřování duše lidí, rozeznávání duševních chorob, nacházení příčin nemocí, duševních útrap a odhalení známek šoku, zklidnění, dodání naděje, vytržení z letargie, léčba duševních chorob a ztráty duševních schopností, podávání odvarů

Vůle (Duše)
» síla vůle, psychická odolnost, nezlomnost a nepoddajnost

Zaříkání (Vliv)
» prokletí (vyvolá ochromení nebo jiné omezení), požehnání (zajistí posílení nebo jiné ulehčení) působící na lidi nebo zvířata
» lámání prokletí a požehnání
» zaklínání míst a věcí ochranným obrazcem pro Zaříkání (Znakem Oka), znalost a lámání ochranných obrazců pro Zaříkání

Vyjednávání s nadpřirozenem (Vliv)
» přilákání rozumných nadpřirozených bytostí, přivolání bytosti v místě či okolí se vyskytující, přesvědčování rozumných nadpřirozených bytostí, argumentace a slovíčkaření, odhad motivací a úmyslů, přímé ovlivňování názorů, křížové výslechy, hovory vedené v hádankách či s posunutým významem');
SET @id_povolanie = LAST_INSERT_ID();
INSERT INTO zvlastna_schopnost (id_povolanie, nazov, vlastnost, citat, text, minimalna_uroven) VALUES
  (@id_povolanie, 'Knihomol', 'duse', 'Zná staré legendy, luští texty v téměř zapomenutých jazycích, rozpozná jakékoliv kouzlo a jeho znalosti ho neomylně přivádějí ke zřídlům čarovné moci.', 'Postava je zběhlá (základní manévry zdarma) v Učenosti a Znalosti nadpřirozena.', 0),
  (@id_povolanie, 'Síla osobnosti', 'duse', 'Jeho klid konejší a jeho moudrost zahání výčitky svědomí i noční můry.', 'Postava je zběhlá (základní manévry zdarma) v Ošetřování lidské duše. Jizvy na Duši dokáže léčit, jako by síla magického okamžiku byla o 1 vyšší. (Aktivace 1 Duše)', 0),
  (@id_povolanie, 'Bystrost', 'duse', 'Je pohotový a v pravou chvíli dokáže pomoci příteli radou či kouzlem.', 'Postava může duševními akcemi reagovat pomocí akce z obrany nejen na akce vedené na sebe, jak je obvyklé, ale i na jakékoliv akce škodící jiné postavě nebo předmětu, který se nachází v dohledu.', 0),
  (@id_povolanie, 'Dar tvoření', 'duse', 'Kde jiní rozhazují zlatý prach a pálí vonné svíce, aby obnovili moc své hole či odznaku víry, on jen zašeptá prosbu a je vyslyšen.', 'Postava dokáže ukládat do svého rituálního předmětu i nouzové Suroviny, zdarma. Postava dokáže vyrábět ochranné obrazce z nouzových Surovin. Za každou takto vloženou Surovinu zaplatí 1 Duši.', 0),
  (@id_povolanie, 'Zřící stráže', 'duse', 'I když je ze svého domu na míle daleko, jako by vám hleděl přes rameno.', 'Postava s tímto zvláštním smyslem dokáže při narušení libovolného svého ochranného obrazce vnímat (slyšet, vidět a cítit) jeho okolí, jako by se na místě sama nacházela.', 0),
  (@id_povolanie, 'Čarovné stráže', 'duse', 'Z prastarých knih vyčetl návody na ochranné rituály, jaké zná jen málokterý kouzelník.', 'Postava dokáže libovolný ochranný obrazec vyrobit jako posílený chytrý ochranný obrazec, který se neaktivuje, jeli splněná podmínka vyslovená při jeho výrobě – například pokud se předmětu dotkne určitá osoba nebo pokud někdo při vstupu do kruhu vyřkne stanovené heslo. (Aktivace 1 Surovina) Postava také dokáže libovolný ochranný obrazec vyrobit jako posílený trvalý ochranný obrazec, který je spoutaný s místem či předmětem velmi dlouho. (Aktivace 3 Suroviny)', 0),
  (@id_povolanie, 'Pán symbolů', 'duse', 'Svou vůlí dokáže spoutat mnoho míst současně, a tak když překonáte jednoho jeho strážce, za rohem může číhat další, ještě mocnější.', 'Postava dokáže udržet spojení s více než jedním ochranným obrazcem. Každé již udržované spojení s ochranným obrazcem zvyšuje náročnost tvoření dalšího ochranného obrazce o 1. Nikdy ale nemůže udržovat spojení s  více než jedním trvalým ochranným obrazcem .', 0),
  (@id_povolanie, 'Divoký talent', 'vliv', 'Síla jeho nadání je udivující. Lidé touží po jeho požehnání a obávají se jeho kleteb.', 'Postava je zběhlá (základní manévry zdarma) v Zaříkání.', 0),
  (@id_povolanie, 'Povolávač', 'vliv', 'S divoženkami a hejkaly si povídá, jako by byli odjakživa kamarádi.', 'Postava je zběhlá (základní manévry zdarma) ve Vyjednávání s nadpřirozenem.', 0),
  (@id_povolanie, 'Modlitby a uřknutí', 'vliv', 'Umí zajistit přízeň nebes, ale také potrestat provinilce tak, že bude na kolenou prosit, aby z něj trest sňal.', 'Postava dokáže seslat posílené Zaříkání (požehnání nebo prokletí), které bude působit dlouho. (Aktivace 1 Vliv) Postava také dokáže seslat posílené prokletí (nikoliv však požehnání), jehož účinky přetrvají velmi dlouho, musí však obsahovat splnitelnou podmínku, jejíž naplnění ji zlomí, například „dokud nepohostíš neznámého pocestného“. (Aktivace 3 Vlivy)', 0),
  (@id_povolanie, 'Mystický kruh', 'vliv', 'Mocným obřadem či rituálem mohou zaříkávači spojit své síly a dosáhnout zázraku.', 'Postava může i mimo svou akci pomoci jiné postavě provádějící nějaký druh magie, připojíli se k jejímu rituálu, a poskytnout jí pro tuto akci odpovídající Zdroje (na zaplacení aktivace, náročnosti, manévrů nebo vyčerpání).', 0),
  (@id_povolanie, 'Věže a chrámy', 'vliv', 'Ať se nachází kdekoliv, dokáže pro své přátele najít ochranu za zdmi kláštera nebo ve věži cechu čarodějů.', 'S touto obsahovou pravomocí může hráč stanovit, že určitá bytost je členem stejného okultního, náboženského či magického spolku jako jeho postava. (Aktivace 1 Vliv)', 0);
