<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration 
 * préfixe de table, clefs secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur 
 * {@link http://codex.wordpress.org/Editing_wp-config.php Modifier
 * wp-config.php} (en anglais). C'est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d'installation. Vous n'avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */


/* Réglages propres à RGS */
define('WP_POST_REVISIONS', false );
define('AUTOSAVE_INTERVAL', 1600000 );




// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'sam_rgs_wordpress');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'root');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', 'root');

/** Adresse de l'hébergement MySQL. */
define('DB_HOST', 'localhost');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8');

/** Type de collation de la base de données. 
  * N'y touchez que si vous savez ce que vous faites. 
  */
define('DB_COLLATE', '');

/**#@+
 * Clefs uniques d'authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant 
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n'importe quel moment, afin d'invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'd81!p71mt11P/gUrgO|UlD#(lCC N*EaKK:xQ@?|[5></ynD6m|yhMiA9vrMV4.R');
define('SECURE_AUTH_KEY',  'v2FpQcYI:7tj>~*cyFJ*C$LQHe0[7^V`w@:ql,g)7O8jjh$Kk.RPdt%`Sltf1d_]');
define('LOGGED_IN_KEY',    'h$6i,U JhYF3m+ppZ!zq7gB211{])H:e@L,M8.XD?*fkoAbD|dVjNgZDg&lYe0tL');
define('NONCE_KEY',        ':+.+~1V*kOA7bRAv(dl*4Hz9o}/j&;FNN+wjpn%BDrH.:7q<`,v.e[c1ElD&1f-{');
define('AUTH_SALT',        '5[+qU@OV<o.2N-v{YE)*XRr;-2Y&uxOt1PB|-t`,*3&x>Q (EM&b#4|^=6!|5[IZ');
define('SECURE_AUTH_SALT', 'C5i^EMkaCif,Rf]tHGkI=^Rk]hq|ytiG6[k~$+XT$E|&L8{ct=h$(7.&kXcqXvpw');
define('LOGGED_IN_SALT',   'R(a^D6c4XfGVty3+IVSL[)Vqw=%ePJhr~jyv-.SElATwR-x,C.91 5C(+,.G=*@d');
define('NONCE_SALT',       'gWw@N,uF%JB>i9(4xd7q8)8P@cDn:N;US5?qbgU-8v65$L/H1OC!J8811J1e2(Ma');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique. 
 * N'utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés!
 */
$table_prefix  = 'wp_';

/**
 * Langue de localisation de WordPress, par défaut en Anglais.
 *
 * Modifiez cette valeur pour localiser WordPress. Un fichier MO correspondant
 * au langage choisi doit être installé dans le dossier wp-content/languages.
 * Par exemple, pour mettre en place une traduction française, mettez le fichier
 * fr_FR.mo dans wp-content/languages, et réglez l'option ci-dessous à "fr_FR".
 */
define('WPLANG', 'fr_FR');

/** 
 * Pour les développeurs : le mode deboguage de WordPress.
 * 
 * En passant la valeur suivante à "true", vous activez l'affichage des
 * notifications d'erreurs pendant votre essais.
 * Il est fortemment recommandé que les développeurs d'extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de 
 * développement.
 */ 
define('WP_DEBUG', false); 

/* C'est tout, ne touchez pas à ce qui suit ! Bon blogging ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');