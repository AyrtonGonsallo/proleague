<?php
function bloc_event() {
	$content_event   = get_sub_field('image_evenement');
	$titre_event   = get_sub_field('titre_event');
	$lieu_event   = get_sub_field('lieu_event');
	$date_event  = get_sub_field('date_event');
    ///$section_bg=get_sub_field('bloc_avec_couleur_de_fond');
    //var_dump($content_event);
	?>
    <section class="section-event">
        <div class="events">
            <div class="image-event" style="background-image: url(<?= $content_event['url']; ?>);"></div>
            <div class="countdown-event">
				<div class="infos-envet">
					<h2><?= $titre_event; ?></h2>
					<p><?= $lieu_event.' - '.$date_event; ?></p>
				</div>
                <div class="count-down-container" id="demo">					
                </div>
            </div>
        </div>
    </section>
<?php
}