<?php
function bloc_partenaires() {
	$liste_des_partenaires = get_field('liste_des_partenaires');    
    $args = array(
        'post_type' => 'partenaires',
        'post_status' => 'publish',
        'posts_per_page' => '10'
    );
        $partenaires_loop = new WP_Query( $args );
        if ( $partenaires_loop->have_posts() ) :
            ?>
            <section class="logo-partenaire">
                <div class="logos-partners">
                    <?php
                    while ( $partenaires_loop->have_posts() ) : $partenaires_loop->the_post();
                        // Set variables
                        $logo = get_field('logo_partenaire');
                        $site_web = get_field('site_partenaire');
                        // Output
                        if( $liste_des_partenaires ):
                            foreach( $liste_des_partenaires as $ldp ):?>
                                <div class="logo-parter">
                                    <a href="<?= $site_web; ?>" target="_blank"><img src="<?= $logo["url"]; ?>" /></a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>                
                    <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>                
                </div>
            </section>
                <?php
        endif;
	?>
<?php
}