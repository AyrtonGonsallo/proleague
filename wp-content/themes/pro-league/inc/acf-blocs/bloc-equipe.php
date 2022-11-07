<?php
function bloc_equipe() {
	$title = get_sub_field('titre_bloc_equipe');
    $liste_des_equipes= get_sub_field('equipes');
	$args = array(
        'post_type' => 'equipes',
        'post_status' => 'publish',
        'posts_per_page' => '16'
    );
        $team_loop = new WP_Query( $args );
        if ( $team_loop->have_posts() ) :
            ?>
            
            <section class="logo-equipe" style="background-image: url(https://judoproleague.com/wp-content/uploads/2022/10/background.webp);">
                <div class="title-gallery">
                    <h2 class="title-logo"><?php echo $title;?></h2>
                </div>
                <div class="team-4-col">
                    <?php 
                        if( $liste_des_equipes ): 
                            foreach( $liste_des_equipes as $team ): 
                                $url=get_permalink($team->ID);
                                $title=get_the_title($team->ID);
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
          endif;
}
