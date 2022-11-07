<?php
function bloc_contact() {
	$map   = get_sub_field('google_map');
	$title   = get_sub_field('titre_bloc_contact');
	$text   = get_sub_field('description_bloc_contact');
    $posts = get_sub_field('formulaire_de_contact');
    //var_dump($posts);exit;
?>

<section class="section-contact" id="devis">
    <div class="container contact">
        <div class="grid-2">
            <div class="map">
                <?= $map; ?>
            </div>
            <div class="contact-form-side txt-col-blanc">
                <h2 class="title-contact"><?= $title; ?></h2>
                <?= $text; ?>
                <div class="btn-div">
                    <?php    
                        if( $posts ): 
                            foreach( $posts as $p ): // variable must NOT be called $post (IMPORTANT) 
                                $cf7_id= $p->ID;
                                echo do_shortcode( '[contact-form-7 id="'.$cf7_id.'" ]' ); 
                            endforeach;
                        endif; 
                    ?>
                </div>
            </div>
        </div>     
    </div>
</section>

<?php
}