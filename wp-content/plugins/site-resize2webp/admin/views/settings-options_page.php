
<h2>Réglages </h2>

<form method="post" action="">
<table class="form-table">
<tr valign="top">
<th scope="row"><label for="on_upload">Media upload</label></th>
<td>
    <?php
    $name=$option_name.'[on_upload]';
    $val=$options['on_upload'];
    echo '<label><input id="on_upload" type="checkbox" name="'.$name.'" value="1" '.checked($val,1,false).'> Process images on upload</label>';
    ?>
</td>
</tr>
<tr valign="top">
<th scope="row"><label >Max image dimension</label></th>
<td>

<?php
//$this->debug('options='.print_r($options,true));

$name=$option_name.'[max_width]';
$val=$options['max_width'];
echo '
<label> Max width
<input type=number name="'.$name.'" class="small-text" value="'.$val.'">
</label>';

$name=$option_name.'[max_height]';
$val=$options['max_height'];
echo '
<label> Max height
<input type=number name="'.$name.'" class="small-text" value="'.$val.'">
</label>';

?>
</td>
</tr>
<tr valign="top">
<th scope="row"><label for="quality">WebP Quality</label></th>
<td>
    <?php
    $name=$option_name.'[quality]';
    $val=$options['quality'];
    echo '<input id="quality" type="number" min="0" max="100" name="'.$name.'" class="small-text" value="'.$val.'">
    <p class="description">plage de 0 (la pire qualité, plus petit fichier) à 100 (meilleure qualité, plus grand fichier).</p>
    ';

    // IMG_WEBP_LOSSLESS declared in PHP 8.1 and later,
    if(defined('IMG_WEBP_LOSSLESS')) {
        $name =$option_name.'[png_lossless]';
        $val  =$options['png_lossless'];
        echo '<br><label><input type="checkbox" name="'.$name.'" value="1" '.checked($val,1,false).'> png lossless</label>';
    }
    else {
        echo '<br><label><input type="checkbox" disabled> png lossless available with PHP 8.1 mini</label>';
    }

    ?>
</td>
</tr>

</table>
<?php
wp_nonce_field('site_resize2webp', 'site_resize2webp_nonce');
?>
<p class="submit">
    <input type="submit" class="button-primary" name="settings_update" value="<?php _e('Save') ?>" />
</p>
</form>
