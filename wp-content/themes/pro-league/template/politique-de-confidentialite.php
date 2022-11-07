<?php

/**
 * Template Name: Politique de confidentialité
 */

get_header();

?>
    

   
<main id="primary" class="site-main home">

<?php
    $title   = get_sub_field('titre_page');
    $text   = get_sub_field('contenu_de_la_page');
    $image  = get_sub_field('photo_header');
    ?>
    <section class="slider">
        <div class="bg-header" style="background-image: url('<?= $image["url"]; ?>');">

        </div>
    </section>
    <section class="services">
        <div class="container">
            <div class="row section-presentation stype-p-pi">
                <div class="col-md-12">
                    <h1 class="style-h1">
                        <?= $title; ?>
                    </h1>
                    <?= nl2br($text); ?>
                </div>
            </div>
        </div>		
    </section>
    
<?php 
get_footer();