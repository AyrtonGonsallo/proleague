<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package pro-league
 */
$site = get_field('site_web');
$description = get_field('presentation'); 
$directeur = get_field('directeur'); 
$entraineur = get_field('entraineur'); 
$date_creation = get_field('date_de_creation'); 
$reseaux= get_field('reseaux_sociaux');
$palmares = get_field('palmares');
$galerie_photos = get_field('galerie_photos');
$gender=  get_field('genre');
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="team-header">
		<?php
        $mypost = get_post($post->ID);
        echo '<div class="info-team">';
			echo '<div class="title-rs">';
				if ( is_singular() ) :
					the_title( '<h1 class="team-title">', '</h1>' );
				else :
					the_title( '<h2 class="team-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
				endif;
				echo '<div class="rs">';
					foreach($reseaux as $rs){
						$rs_link=$rs['lien_page'];
						if($rs["type"]=="Facebook"){
							echo '<a href="'.$rs_link.'" target="_blank"><i class="fa-brands fa-square-facebook"></i></a>';
						}
						elseif($rs["type"]=="Instagram"){
							echo '<a href="'.$rs_link.'" target="_blank"><i class="fa-brands fa-instagram"></i></a>';
						}
						elseif($rs["type"]=="Tiktok"){
							echo '<a href="'.$rs_link.'" target="_blank"><i class="fa-brands fa-tiktok"></i></a>';
						}
						elseif($rs["type"]=="YouTube"){
							echo '<a href="'.$rs_link.'" target="_blank"><i class="fa-brands fa-youtube"></i></a>';
						}
						elseif($rs["type"]=="Twitter"){
							echo '<a href="'.$rs_link.'" target="_blank"><i class="fa-brands fa-twitter"></i></a>';
						}
					}
					echo '</div>';
				echo '</div>';
		echo '<div class="all-site">';
            echo '<div class="div-site"><a href="https://www.'.$site.'/" class="link-team" target="_blank">'.$site.'</a></div>';
            echo'<div class="info-club">';
			if($date_creation){
					echo "<b>Date de création: </b>".$date_creation."<br>";
				}
			if($directeur){
				echo "<b>Président : </b>".$directeur."<br>";
			}
			if($entraineur){
					echo "<b>Entraîneur : </b>".$entraineur."<br>";
				}
        echo '</div>';
		echo '</div>';
		echo '</div>';
        ?>
        <?php pro_league_post_thumbnail(); ?>
	</header>


	<div class="team-content">
		<?php 
		if($description){
		?>
        <div class="description">
            <h2>La Team <?= the_title(); ?></h2>
            <?= $description;?>
        </div>
		<?php 
		}?>
		
		<div class="gender">
			
			<div class="feminine">
				<h2 class="title-genre">
					Féminines
				</h2>
				<?php 
					foreach($gender as $genre){
						//var_dump($genre);exit();
						if($genre["genre_joueur"]=='Féminine'){
							echo '<h3>'.$genre["nom_joueur"].'</h3>';
						}
				?>
				<?php
					}
				?>
			</div>
			<div class="masculin">
				<div class="feminine">
				<h2 class="title-genre">
					Masculins
				</h2>
					<?php 
						foreach($gender as $genre){
							//var_dump($genre);exit();
							if($genre["genre_joueur"]=='Masculin'){
								echo '<h3>'.$genre["nom_joueur"].'</h3>';
							}
					?>
					<?php
						}
					?>			
				</div>
			</div>
		</div>
		<?php
			if($palmares){
		?>
		<div class="palmares">
            <h2>Palmarès</h2>
            <?php 
            foreach($palmares as $palma){
                echo '<p class="p-palmares">'.$palma["championnat"].'</p>';
            }
            ?>
        </div>		
		<?php 
		}
		if($galerie_photos){
		?>
		<div class="galerie-equipe">
            <h2>Photos <?= the_title(); ?></h2>
            <?php 
            echo '<div class="galerie-team">';
                foreach($galerie_photos as $gp){
                    $url_photo=$gp["url"];
                        echo '<img src="'.$url_photo.'" class="gp-team">';
                }
            echo '</div>';
            ?>
        </div>
		<?php
		}
		?>
	</div><!-- .entry-content -->
	
	<?php
	$titlebar   = get_field('header_de_la_page');
$title   = get_field('titre_de_la_page');
$liste_des_equipes = get_field('liste_des_equipes');
?>
    <section class="listes-equipes">
        <div>
            <h1 class="h1-equipes"><?= $title; ?></h1>
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
                <a href="<?= $url; ?>">
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
    ?>
</article>
