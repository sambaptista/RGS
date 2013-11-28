<?php

class JeuxStrategieControler
{

    public static $fiches_orphelines = array(
        '100000' => 'Age Of Wonders : Shadow Magic',
        '100001' => 'Battle Mages',
        '100002' => 'Battle Realms',
        '100003' => 'Chaos League',
        '100004' => 'Dominions 2 ',
        '100005' => 'Etherlords II',
        '100006' => 'KOHAN : Immortal Sovereigns',
        '100007' => 'La Bataille pour la Terre du Milieu',
        '100008' => 'La Bataille pour la Terre du Milieu II',
        '100009' => 'La Bataille pour la Terre du Milieu II : l AvËnement du Roi-Sorcier ',
        '100010' => 'Spellforce',
        '100011' => 'The Battle for Wesnoth',
        '100012' => 'Warlords Battlecry II',
        '100013' => 'Warlords Battlecry III',
        '100014' => 'Command and Conquer 3: La fureur de Kane',
        '100015' => 'Conquest: Frontier Wars',
        '100016' => 'Empereur: la Bataille pour Dune',
        '100017' => 'Forged Alliance',
        '100018' => 'Ground Control 2 : OpÈration Exodus ',
        '100019' => 'HomeWorld 2',
        '100020' => 'Massive Assault',
        '100021' => 'Project Visitor',
        '100022' => 'Sins of a Solar Empire : Entrenchment',
        '100023' => 'Star Warsô Galactic Battlegroundsô',
        '100024' => 'UFO : Aftermath',
        '100025' => 'Warhammer 40 000 : Dawn of War',
        '100026' => 'Warhammer 40 000 : Dawn of War 2 - Chaos Rising',
        '100027' => 'Age of Kings : The Conquerors',
        '100028' => 'Alerte Rouge 2',
        '100029' => 'American Conquest : Fight Back',
        '100030' => 'Anno 1503 : le nouveau monde',
        '100031' => 'Chariots of War',
        '100032' => 'Civilization 3',
        '100033' => 'Command & Conquer Generals',
        '100034' => 'C&C Generals : Heure H',
        '100035' => 'Cossacks : European Wars',
        '100036' => 'Crown of the north',
        '100037' => 'Europa 1400',
        '100038' => 'Europa Universalis',
        '100039' => 'Hearts of Iron',
        '100040' => 'Medieval Lords',
        '100041' => 'Medieval Total War',
        '100042' => 'Montjoie',
        '100043' => 'Rise of Nations',
        '100044' => 'StrongHold',
        '100045' => 'Stronghold Crusader ',
        '100046' => 'The Gladiators',
        '100047' => 'Victoria',
        '100048' => 'War and Peace',
        '100049' => 'Warrior Kings',
        '100050' => 'Advance Wars',
        '100051' => 'Advance Wars 2 : black hole rising',
        '100052' => 'Advance Wars DS',
        '100053' => 'Airborne Assault',
        '100054' => 'Darwinia',
        '100055' => 'Final Fantasy Tactics Advance',
        '100056' => 'Football Manager 2009',
        '100057' => 'Impossible Creatures',
        '100058' => 'No Man s Land',
        '100059' => 'Galactic Civilisation II',
        '100060' => 'Republic',
        '100061' => 'Hearthstone',
        '100062' => 'Dark End',
        '100063' => 'Birth of America ',
        '100064' => 'Jagged Alliance 3D ',
        '100065' => 'Defenders of Ardania',
        '100066' => 'Majesty 2',
        '100067' => 'Age of Empires 3',
        '100068' => 'Deus Ex 3',
        '100069' => 'Dragon Commander',
        '100070' => 'Galactic Assault',
        '100071' => 'Hearts of Iron 2',
        '100072' => 'Jumpgate Evolution ',
        '100073' => 'Kingdom Under Fire II',
        '100074' => 'Rise of Immortals',
        '100075' => 'Supreme Ruler 1936',
        '100076' => 'The Settlers VI',
        '100077' => 'Victoria 2 : A House Divided',
        '100078' => 'Wargame Airland Battle'
    );




     public function __construct()
     {
         $this->teo = new TypoExporterController();

         $bdd = RGSBD::getInstance();
         $bdd->resetWP();



     }








}