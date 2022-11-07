<?php
function bloc_une() {
	$news   = get_sub_field('article');
	$i=0;
?>

<section class="section-a-la-une">
        <div class="cta-txt">
        <?php 
                if( $news ): 
            ?>
                <?php 
                    foreach( $news as $my_post ):						
						$i=$i+1;
                       //print_r($my_post);exit();
                        $img= get_the_post_thumbnail_url($my_post->ID,'full');
                        $url=get_permalink($my_post->ID);
                        $content = $my_post->post_content;
                        $excerpt = substr($content, 0, 230);
                    ?>
                    <div style="" class="slide mobile<?= $i;?>">
                        <div class="img-une" style="background-image: url(<?= $img; ?>);"></div>
                        <div class="news-content">
                            <a href="<?= $url; ?>" class="news-link"><h3 class="title-news-3-col"><?= $my_post->post_title?></h3></a>
                            <span><?php echo date("d M Y", strtotime($my_post->post_date)); ?></span>
                        </div>
                    </div>
                <?php
			endforeach; ?>
            <?php endif; ?>        
        </div>
</section>

<?php
}