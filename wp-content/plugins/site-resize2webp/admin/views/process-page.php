<h2>Resize 2 webp</h2>


<?php
if($action=='attid' && $attid && $result) {

	extract($result);
	$filename=$file[0];
	echo '<h3>Traitement de '.$filename.'</h3>';


  if(!$do_convert && !$do_resize) {
  	echo '<p>rien à faire</p>';
    if(!empty($details)) {
      echo '<ul><li>';
      echo implode('</li><li>',$details);
      echo '</li></ul>';
    }
  }
  else {
  	$res=$this->att_resize($result);
  	if($res['error']) {
  		echo '<p>ERREURS</p>';
  		echo '<ul><li>';
  		echo implode('</li><li>',$res['error']);
  		echo '</li></ul>';
  	}
  	if(!empty($res['msg'])) {
  		echo '<p>'.implode('<br>',$res['msg']).'</p>';
  	}
  	echo '<p>terminé</p>';
  }

}
?>