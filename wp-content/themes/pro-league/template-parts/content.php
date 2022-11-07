<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package pro-league
 */

?>
<div>
<article id="myarticles post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="thumb-article">
		<?php pro_league_post_thumbnail(); ?>
	</div>
	<div class="content-article">
		<header class="entry-header">
			<?php
			if ( is_singular() ) :
				the_title( '<h1 class="entry-title">', '</h1>' );
			else :
				the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			endif;

			if ( 'post' === get_post_type() ) :
				?>
				<div class="entry-meta">
					<?php
					//pro_league_posted_on();
					//pro_league_posted_by();
					?>
				</div><!-- .entry-meta -->
			<?php endif; ?>
		</header><!-- .entry-header -->
		<div class="entry-content">
			<?php
			$post_type=get_the_title( get_option('page_for_posts', true) );
			//var_dump($post_type);exit();
			if($post_type=="Actualités" && !is_singular() ){
				$excerpt = get_the_excerpt();
				echo wp_trim_words( $excerpt, 22, '  [...]' ); 
			}
			elseif(is_singular() ){
				the_content(
					sprintf(
						wp_kses(
							/* translators: %s: Name of current post. Only visible to screen readers */
							__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'pro-league' ),
							array(
								'span' => array(
									'class' => array(),
								),
							)
						),
						wp_kses_post( get_the_title() )
					)
				);
			}
			
			?>
		</div><!-- .entry-content -->
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
		</div>