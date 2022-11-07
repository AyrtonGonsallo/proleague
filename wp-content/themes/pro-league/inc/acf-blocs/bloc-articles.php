<?php
function bloc_articles() {
    $news   = get_sub_field('articles');
    //var_dump($news);exit();
    ?>
    <section class="section-articles">
        <div class="news-3-col">
        <?php 
                if( $news ): 
            ?>
                <?php 
                    foreach( $news as $my_post ): 
                        //print_r($my_post);exit();
                        $img= get_the_post_thumbnail_url($my_post->ID,'full');
                        $url=get_permalink($my_post->ID);
                        $content = $my_post->post_content;
                        $excerpt = substr($content, 0, 230);
                    ?>
                    <div class="display-news-3-col">
                        <div class="news-img-2-col" style="background-image: url(<?= $img; ?>);"></div>
                        <div class="right-content">
                            <a href="<?= $url; ?>" class="news-link-2-col"><h3 class="title-news-3-col"><?= $my_post->post_title?></h3></a>
                            <span><?php echo date("d M Y", strtotime($my_post->post_date)); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>        
        </div>
    </section>
<?php
}