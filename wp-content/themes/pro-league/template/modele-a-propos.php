<?php

/**
 * Template Name: Modèle A-propos
 */

get_header();
$titlebar   = get_field('header_de_la_page');
$title   = get_field('titre_de_la_page');
?>

<main id="primary" class="site-main ">
    <section class="listes-equipes page-about">
        <?php the_content(); ?>
    </section>

    <?php
get_footer();
?>