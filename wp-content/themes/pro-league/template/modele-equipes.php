<?php

/**
 * Template Name: Modèle equipe
 */

get_header();
$titlebar   = get_field('header_de_la_page');
$title   = get_field('titre_de_la_page');
$liste_des_equipes = get_field('liste_des_equipes');
?>

<main id="primary" class="site-main home">
    <section class="listes-equipes">
        <div>
            <h1 class="h1-equipes"><?= $title; ?></h1>
        </div>
		<div class="img-fr">
			<img src="https://judoproleague.com/wp-content/uploads/2022/11/carte-france-equipes-judo-pro-league-1.webp">
		</div>
        <div class="team-4-col">
            <?php 
                if( $liste_des_equipes ): 
                    foreach( $liste_des_equipes as $lde ): 
						$img= get_the_post_thumbnail_url($lde->ID,'thumbnail');
                        $url=get_permalink($lde->ID);
                        $title=get_the_title($lde->ID);
			?>
            <div class="equipe">
                <a>
                    <h3 class="text-white"><?= $title;?></h3>
                </a>
            </div>
            <?php 
                endforeach;    
                endif; 
            ?>
        </div>
    </section>

    <?php
    while (have_rows('liste_des_partenaires'))
    {
        the_row();
        $style=get_row_layout();
        //site_debug("style=$style");
        $section=false;
        switch($style)
        {    
            case 'partenaires':
            require_once (THEMEDIR.'inc/acf-blocs/bloc-partenaires.php');
            $section=bloc_partenaires();
            break;
        }
    }
get_footer();
?>