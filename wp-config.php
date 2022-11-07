<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en « wp-config.php » et remplir les
 * valeurs.
 *
 * Ce fichier contient les réglages de configuration suivants :
 *
 * Réglages MySQL
 * Préfixe de table
 * Clés secrètes
 * Langue utilisée
 * ABSPATH
 *
 * @link https://fr.wordpress.org/support/article/editing-wp-config-php/.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define( 'DB_NAME', 'aoutfscjudopro' );

/** Utilisateur de la base de données MySQL. */
define( 'DB_USER', 'root' );

/** Mot de passe de la base de données MySQL. */
define( 'DB_PASSWORD', '' );

/** Adresse de l’hébergement MySQL. */
define( 'DB_HOST', 'localhost' );

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/**
 * Type de collation de la base de données.
 * N’y touchez que si vous savez ce que vous faites.
 */
define( 'DB_COLLATE', '' );

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clés secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'L^ D0m&HFcHGIlg2%bE>a,Rj8I=8Z/-!V@ekWMq)Crub.[4:Sw4cK9}F#;.?2MC_' );
define( 'SECURE_AUTH_KEY',  'R&|XGGHGD%d,Y671qiW/%oSRszi%@)&ar`;KGhNnbg+tP~WAGXo]93jFVyJm<P%)' );
define( 'LOGGED_IN_KEY',    'u<@)1.]P~EaShwtlTLs~R>:As//)g/>To]EfWa#9jA]?xQSd[<>UWx}s>Gu}/!f1' );
define( 'NONCE_KEY',        '8:b!^ ZX<{CTeSP]V/yCw{Iw42h5=I@::{F!|p^JsxPU7`PJ9+dLB{:5?EaJb3sv' );
define( 'AUTH_SALT',        'seRg0K5ObI/]W!K2AM^khH8(@Fxz6kFUj6y#VQ}%6|,{u(G=[/JU}svbsk<1}}=A' );
define( 'SECURE_AUTH_SALT', 'Qb!Ww3)@)g$5V_b=tF}t2(]B6t5GTb4eQJwXY.gO`3Sp]VFr^#&tQ9{QQNJ9SS|H' );
define( 'LOGGED_IN_SALT',   '5 Wbg5bx09myxC6LJ5u]@wMX]fLz:sNx@i4rXWy8zt?Z3@^o;.y 3_I~ TI`aCwh' );
define( 'NONCE_SALT',       'X_`;0@izBbC|a8%>duB_QCx;PxKXZv#b|n)TJ(.FJEDtpX(yo*tWB~^Eh%w;L|`-' );
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix = 'prol_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortement recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://fr.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* C’est tout, ne touchez pas à ce qui suit ! Bonne publication. */

/** Chemin absolu vers le dossier de WordPress. */
if ( ! defined( 'ABSPATH' ) )
  define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once( ABSPATH . 'wp-settings.php' );
