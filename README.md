# Správca Drd denníkov

Samuel Krcho

5ZYI34

## Stručný opis projektu
Projekt má pomôcť pri vytváraní a správe denníkov stolovej hry Dračí doupě, vďaka čomu sa môže vyhnúť fyzickej kópií denníkov a upravovať dané denníky pomocou daných pravidiel hry.
Existujú správcovia denníkov na podobné hry (ako Dungeons and dragons), ale nie špecificky na hry Dračí doupě.

## Role v projekte
Používatelia:
  - guest - neprihlásený používateľ si bude môcť prezerať verejné denníky vytvorené inými používateľmi a bude mať prístup k pravidlám herných postáv
  - user - prihlásený používateľ si bude môcť vytvoriť a spravovať vlastné denníky postáv + guest
  - GM (game master) - vie získať prítup k správe denníkov používateľov, ktorí mu dajú na to povolenie (pre jednoduchý prístup k postavám danej hry) + user
  - admin - má prístup ku všetkým denníkom + GM

## Prípady použitia podľa rolí
Guest: vie si prezerať denníky, nevie ich však vytvoriť
User: dokáže si vytvoriť denník postavy podľa pravidiel, tieto denníky vie upravovať podľa potreby - vyšší level, pridanie výzbroje, predmetov,...
GM: user ktorý dokáže spravovať iných user-ov pre prehľadnejší beh hry - má tieto denníky rýchlo k dispozícií a vie ich aj upravovať
Admin: keby bolo treba má prístup ku všetkým denníkom

## Plánované entity
Denník: obsahuje základné informácie o denníku postavy - majiteľ denníku, predmety, vybavenie, povolanie, zdroje
Používatel: definuje používateľa - rola, id, hash(heslo)
Predmet: popisuje typ predmetu v inventári postavy - typ predmetu, popis
Povolanie: popisuje role postavy (bojovník, lovec,...) a jeho váhu (čím viac tým lepšia je postava v tom povolaní - typ povolania, počet bodov povolania
Zvláštne schopnosti: vypísané špeciálne schopnopsti podľa povolania postavy - typ schopnosti, popis schopnosti

## Vzťahy medzi entitami
Pouzivatel <-> Dennik -> 1:N
Dennik <-> Povolanie, Predmet -> M:N
Povolanie <-> Zvláštne schopnosti -> 1:N

## Hlavné stránky aplikácie
1. Zoznam denníkov - vypísané verejné denníky - obrázok + popis denníkov
2. Denník postavy - špecifický denník postavy - graficky vykreslený denník postavy
3. Info povolania - zoznam povolaní, ich popis
4. Info zvláštne schopnosti - zoznam zvláštnnych schopností podľa povolaní
5. Tvorba denníku - postupný proces tvorby denníku
6. Prihlasovanie - popup na prihlásenie sa do webstránky
7. Search - možnosť vyhľadať denníky podľa mena, používateľa, alebo iných vlastností, alebo rýchle vyhľadanie povolania/zvláštnej schopnosti

## Rozdelenie funkcionality
Rozdeľte plánované funkcie na základné a rozširujúce. Základné funkcie predstavujú minimum potrebné na úspešné dokončenie projektu, zatiaľ čo rozširujúce funkcie môžu byť implementované navyše podľa Vašich časových možností.
Základné funkcie:
  - tvorba a úprava denníkov
  - informácie a používanie základných povolaní (s ich príslušnými zvláštnymi schopnosťami)
  - grafické vykreslenie denníkov podľa existujúcich (papierových) denníkov
Rozširujúce funkcie:
  - animácie
  - prístup k jednotlačítkovému hodu kockou s automatickým pripočítaním všetkých relevantných hodnôt
