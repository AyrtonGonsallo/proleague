<?php

class site_resize2webp_admin
{
	private $parent;
  public $menu_slug = 'resize2webp';
  private $hook_suffix;
  public $capability =  'manage_options'; // droit pour acceder a la page reglages

  // formats a traiter
  public $valid_formats=['image/png','image/jpeg','image/jpg','image/webp'];
  public $convert_formats=['image/png','image/jpeg','image/jpg'];

  public $process_menu_slug = 'resize2webp_process';
  private $menu_process; // media_page_resize2webp_process

	function __construct($parent)
	{
		$this->parent=$parent;
    /*
    page reglages
    */
    add_action( 'admin_menu', [$this, 'add_admin_menu' ] );
    //  racourci reglages sur la page plugin
    add_filter( 'plugin_action_links', [$this,'action_links'], 10, 2 );


    // Hook the function to the upload handler
    if( $this->parent->options['on_upload'] ) {
      add_action('wp_handle_upload', [$this,'uploadresize']);
    }

    // add media column
    add_filter( 'manage_media_columns', [$this,'add_column_name' ] );
    add_action( 'manage_media_custom_column', [$this,'add_column_value'], 10, 2 );

    // add option page under medias
	}


  function add_column_name($cols)
  {
    $cols['resizewebp']='Webp';
    return $cols;
  }

  function add_column_value($column_name, $id)
  {
    if($column_name!=='resizewebp') return;

    $file=wp_get_attachment_image_src($id,'full');

    $filename=$file[0]; // https://fraicheur.ape-com.xyz/wp-content/uploads/2022/03/road.svg

    // skip webp extension
    $info = pathinfo($filename);
    $ext=strtolower($info['extension']);
    if($ext=='webp' or $ext=='svg') return;
    //if($ext=='webp' ) return;

    //$res=$this->get_actions(compact('max_width','max_height','mime','width','height'));
    $res=$this->get_actions($id);
    if(!$res) return;
    extract($res);
    $permalink = admin_url( 'upload.php' ).'?page='.$this->process_menu_slug.'&action=attid&attid='.$id;
    if($do_convert && $do_resize) echo '<a href="'.$permalink.'" target="_blank">Reduire et Convertir en webp</a>';
    else if($do_convert) echo '<a href="'.$permalink.'" target="_blank">Convertir en webp</a>';
    //else if($do_resize) echo 'Reduire'; // deja en webp mais trop grand
    if(!empty($details)) echo '<br>'.implode(', ',$details);

  }

  function get_actions($attid)
  {
    if(empty($attid)) return false;
    $details=[];

    $meta = wp_get_attachment_metadata( $attid );
    // check mime first size
    $current_size=current($meta['sizes']);

    $mime=$current_size['mime-type'];
    $do_convert=false;
    $do_resize=false;
    $sizes=[];
    $file=wp_get_attachment_image_src($attid,'full');
    $filename=$file[0]; // https://fraicheur.ape-com.xyz/wp-content/uploads/2022/03/road.svg
    $image_editor = wp_get_image_editor($filename);
    if(is_wp_error($image_editor)) {
      $details[]='cant start editor '.$filename.' ('.$image_editor->get_error_message().')';
    }
    else {
      $sizes = $image_editor->get_size();

      $width  = !empty($sizes['width'])?$sizes['width']:'';
      $height = !empty($sizes['height'])?$sizes['height']:'';

      $options=$this->parent->get_options();
      $max_width  =$options['max_width'];
      $max_height =$options['max_height'];

      if(!in_array($mime,$this->valid_formats)) {
        $details[]='not valid format ('.$mime.')';
      }
      else{
        $do_resize=( ($width > $max_width) or ($height > $max_height) );
        if($do_resize) {
          if($width > $max_width) $details[]='reduce width '.$width.' to '.$max_width;
          if($height > $max_height) $details[]='reduce height '.$height.' to '.$max_height;
        } else $details[]='size ok';
        $do_convert=in_array($mime,$this->convert_formats);
        if($do_convert) $details[]='convert '.$mime.' to webp';
        else $details[]='mime ok '.$mime;
      }
    }
    return compact('do_resize','do_convert','details','file','sizes','attid','mime','image_editor','meta');
  }

  function uploadresize($image_data,$debug=0)
  {
    /*
    $image_data = Array (
      [file] => /home/mvvn8107/theme-dev.ape-com.xyz/wp-content/uploads/2022/03/calendrier-production-hiver.png
      [url] => https://theme-dev.ape-com.xyz/wp-content/uploads/2022/03/calendrier-production-hiver.png
      [type] => image/png
    )
    */
    $this->debug('uploadresize()',true);
    $this->debug($image_data,true);

    if(empty($image_data['type']) ) {
      $this->debug('empty image_data[type]',true);
      return $image_data;
    }
    if(empty($image_data['file'])) {
      $this->debug('empty image_data[file]',true);
      return $image_data;
    }

    // unkown format
    if(!in_array($image_data['type'],$this->valid_formats)) {
      $this->debug('skipping format '.$image_data['type'],true);
      return $image_data;
    }

    // resize
    $image_editor = wp_get_image_editor($image_data['file']);
    $sizes = $image_editor->get_size();

    $width  = !empty($sizes['width'])?$sizes['width']:'';
    $height = !empty($sizes['height'])?$sizes['height']:'';
    if(!$width or !$height ) {
      $this->debug('skip no size w='.$width.' h='.$height,true);
      return $image_data;
    }


    $options=$this->parent->get_options();
    $max_width  = $options['max_width'];
    $max_height = $options['max_height'];


    $do_convert=$do_resize=true;

    // resize not needed
    if( ($width < $max_width) && ($height < $max_height) ) {
      $this->debug('no resize w='.$width.' h='.$height,true);
      $do_resize=false;
    }
    else $this->debug('resize needed w='.$width.' h='.$height,true);

    $image_type = $image_data['type'];
    if($image_type=='image/webp') {
      $this->debug('no convert image_type='.$image_type,true);
      $do_convert=false;
    }

    if(!$do_resize && !$do_convert) {
      $this->debug('end',true);
      return $image_data;
    }

    if($do_resize) {
      $result = $image_editor->resize($max_width, $max_height, false);

      // desactivé car renvoie une erreur alors que le resize a fonctionné
      /*if(is_wp_error($result))
      {
        // error
        $this->debug('ERROR resize : '.$result->get_error_message(),true);
        $this->debug('end',true);
        return $image_data;
      }*/
      $new_sizes = $image_editor->get_size();
      $this->debug('resize done w='.$new_sizes['width'].' h='.$new_sizes['height'],true);

      //$this->debug('end test no save',true);return $image_data;

      $saved_image = $image_editor->save($image_data['file']);
      if(is_wp_error($saved_image))
      {
        // error
        $this->debug('ERROR when saving : '.$result->get_error_message(),true);
        $this->debug('end',true);
        return $image_data;
      }

      //  'path'=>string, 'file'=>string, 'width'=>int, 'height'=>int, 'mime-type'=>string
      $this->debug('OK save to format '.$saved_image['mime-type'].' w='.$saved_image['width'].' h='.$saved_image['height'],true);
    }

    if($do_convert) {
      $quality    = $this->parent->quality($image_type);
      $image_data = $this->convert_webp( $image_data, $quality ,$debug);
    }

    $this->debug('end',true);
    return $image_data;
  }

  function convert_webp($image_data, $compression_level=75,$debug=0) {

    // https://stackoverflow.com/questions/67183333/wordpress-convert-image-to-webp-format-programmatically-with-gd-image-engine
    $file = $image_data['file'];

    $info = pathinfo($file);
    /* info
      [dirname] => /home/mvvn8107/theme-dev.ape-com.xyz/wp-content/uploads/2022/03
      [basename] => calendrier-production-hiver-5.png
      [extension] => png
      [filename] => calendrier-production-hiver-5
    */
    //$this->debug('info');$this->debug($info);

    switch ( strtolower($info['extension']))
    {
      case 'jpeg':
      case 'jpg':
      $image = @imagecreatefromjpeg( $file );
      if(!$image) {
        $this->debug('ERROR imagecreatefromjpeg file='.$file,true);
        return $image_data;
      }
      break;

      case 'png':
      $image = @imagecreatefrompng( $file );
      if(!$image) {
        $this->debug('ERROR imagecreatefrompng file='.$file,true);
        return $image_data;
      }
      imagepalettetotruecolor( $image );
      imagealphablending( $image, true );
      imagesavealpha( $image, true );
      break;

      default:
      return $image_data;
      break;
    }

    $new_name = $info['filename'].'.webp'; // calendrier-production-hiver-5.webp
    $dest     = $info['dirname'].'/'.$new_name; // /home/mvvn8107/theme-dev.ape-com.xyz/wp-content/uploads/2022/03/calendrier-production-hiver-5.webp

    $this->debug("new_name=$new_name");
    $this->debug("dest=$dest");

    $r=imagewebp( $image, $dest, $compression_level );
    if(!$r) {
      $this->debug('ERROR saving imagewebp dest='.$dest,true);
    }
    imagedestroy( $image );

    if($debug==0)unlink($file); // efface l'original

    $image_data['file']=$dest;
    $old_url=$info['basename'];
    $this->debug("old_url=$old_url");
    $image_data['url']=str_replace($old_url,$new_name,$image_data['url']);
    $this->debug("new_url=".$image_data['url']);
    $image_data['type']='image/webp';

    return $image_data;
  }


	function debug($txt,$to_file=false)
  {
    if(is_array($txt) or is_object($txt))
      $this->parent->debug('admin:'.print_r($txt,true),$to_file);
    else
      $this->parent->debug('admin:'.$txt,$to_file);
  }

  function action_links( $links, $file )
  {
    if ( $file == 'site-resize2webp/index.php' )
    {
      $url = "options-general.php?page=".$this->menu_slug;
      $settings_link = '<a href="' . esc_attr( $url ) . '">'. esc_html( __( 'Réglages' ) ) . '</a>';
      array_unshift( $links, $settings_link );
    }

    return $links;
  }

  function add_admin_menu()
  {
    // page reglages
    $page_title   = 'Réglages Resize2webp';
    $menu_title   = 'Resize2webp';

    $this->hook_suffix=add_options_page( $page_title, $menu_title, $this->capability, $this->menu_slug, [$this,'option_page_content'] );

    // page process
    $page_title   = 'Traitement par lot Resize2webp';
    $menu_title   = 'Resize2webp';
    $parent_slug  = 'upload.php';
    $this->menu_process=add_submenu_page( $parent_slug, $page_title, $menu_title, $this->capability, $this->process_menu_slug, [$this,'process_page_content'] );

  }

  function get_settings_action()
  {

    $action = 'show';
    $update_buttons = array(
      'settings_update',
      );
    foreach($update_buttons as $update_button)
    {
      if (!isset($_REQUEST[$update_button])) {
        continue;
      }
      if (!wp_verify_nonce($_POST['site_resize2webp_nonce'], 'site_resize2webp')) {
        wp_die('Security check failed');
      }
      $action = $update_button;
      break;
    }

    return $action;
  }

  function update_general_options()
  {
    $options=$_REQUEST[$this->parent->option_name];
    if(empty($options['on_upload'])) {
      $options['on_upload']=false;
    }
    if(empty($options['png_lossless']) && defined('IMG_WEBP_LOSSLESS')) {
      $options['png_lossless']=false;
    }
    $this->parent->update_option($options);
    return $options;
  }

  function option_page_content()
  {
    $action = $this->get_settings_action();
    switch ($action) {
      case 'settings_update':
      $options=$this->update_general_options();
      break;
      default :
      $options=$this->parent->get_options();
      break;
    }
    //$this->debug("action=$action");
    $option_name=$this->parent->option_name;

    $this->debug($options);

    include(plugin_dir_path(__FILE__).'views/settings-options_page.php');

    // test convert
    if(0) {
      $url='https://theme-dev.ape-com.xyz/wp-content/uploads/2022/03/calendrier-production-hiver-5.png';
      $file='/home/mvvn8107/theme-dev.ape-com.xyz/wp-content/uploads/2022/03/calendrier-production-hiver-5.png';

      $image_data=$this->convert_webp(compact('url','file'),75,1);
      echo 'DONE<br><pre>';
      print_r($image_data);
      echo '</pre>';
    }
  }


  function process_page_content()
  {
    $action=filter_input(INPUT_GET, 'action',FILTER_SANITIZE_EMAIL);
    $result=false;
    switch($action)
    {
      case 'attid':
      $attid=filter_input(INPUT_GET, 'attid',FILTER_SANITIZE_NUMBER_INT);
      $result=$this->get_actions($attid);
      break;

      default:
      break;
    }
    include(plugin_dir_path(__FILE__).'views/process-page.php');
  }

  function att_resize($data)
  {
    // $data='do_resize','do_convert','details','file','sizes','attid','mime','image_editor','meta');
    extract($data);

    //$this->debug('att_resize('.$attid.')');
    $result=['error'=>[],'msg'=>[]];

    $filename=get_attached_file($attid);

    $total_size=$this->get_total_size($filename,$meta);
    $result['msg'][]='total images size='.size_format($total_size,2);

    //$result['msg'][]='meta';$result['msg'][]='<pre>'.print_r($meta,true).'</pre>';

    /*$result['msg'][]='image_data';
    $result['msg'][]='<pre>'.print_r($image_data,true).'</pre>';*/

    $debug=0; // delete original
    //$debug=1; // keep original

    //
    // traite la grande image
    //
    $url=$file[0];
    $image_data=[
      'file' => $filename, // /home/mvvn8107/fraicheur.ape-com.xyz/wp-content/uploads/2022/03/entreprot-fraicheur-lyonnaise-scaled.jpg
      'url'  => $url, // https://fraicheur.ape-com.xyz/wp-content/uploads/2022/03/entreprot-fraicheur-lyonnaise-scaled.jpg
      'type' => $mime,
    ];

    $new_image_data=$this->uploadresize($image_data,$debug);
    $new_filename=$new_image_data['file'];
    /*
    $new_image_data = Array (
      [file] => /home/mvvn8107/theme-dev.ape-com.xyz/wp-content/uploads/2022/03/calendrier-production-hiver.png
      [url] => https://theme-dev.ape-com.xyz/wp-content/uploads/2022/03/calendrier-production-hiver.png
      [type] => image/webp
    )
    */

    //
    // convert intermediate size
    //
    if(1) {
    $filepath = dirname( $filename );
    $base_url= dirname($new_image_data['url']); // better to use pathinfo()

    $new_meta=$this->convert_att($filepath,$base_url,$meta,$debug);
    $new_meta['file']=str_replace(basename($new_meta['file']),basename($new_filename),$new_meta['file']);
    }

    // update wp db
    $this->update_db($attid,$new_image_data,$new_meta,$debug);

    if($new_meta) {
      $new_total_size=$this->get_total_size($new_filename,$new_meta);
      $gain=$total_size - $new_total_size;
      $result['msg'][]='new images size='.size_format($new_total_size,2);
      $result['msg'][]='gain = '.size_format($gain,2).' '.$gain.' bytes';
    }


    return $result;
  }

  function get_total_size($filename,$meta=[])
  {
    $total_size=0;

    $total_size+=$size=@filesize($filename);
    $this->debug($filename. 'size='.$size.' total='.$total_size);

    if ( !isset( $meta['sizes'] ) or !is_array( $meta['sizes'] ) ) return $total_size;

    $filepath = dirname( $filename );
    foreach ( $meta['sizes'] as $size => $sizeinfo ) {
      /*
       [medium] => Array
              (
                  [file] => test-plugin-e1646756789935-540x231.png
                  [width] => 540
                  [height] => 231
                  [mime-type] => image/png
              )
      */
      $intermediate_file=$filepath.'/'.$sizeinfo['file'];
      $total_size+=$size=@filesize($intermediate_file);
      //$this->debug($intermediate_file. ' size='.$size.' total='.$total_size);
    }

    return $total_size;
  }

  function convert_att($filepath,$base_url,$meta,$quality,$debug=0)
  {
    $new_meta=$meta;
    foreach ( $meta['sizes'] as $size => $sizeinfo )
    {
      /*
       [medium] => Array
              (
                  [file] => test-plugin-e1646756789935-540x231.png
                  [width] => 540
                  [height] => 231
                  [mime-type] => image/png
              )
      */
      $quality    = $this->parent->quality($sizeinfo['mime-type']);

      $intermediate_file=$filepath.'/'.$sizeinfo['file'];
      $url=$base_url.'/'.$sizeinfo['file'];
      $image_data=[
        'file' => $intermediate_file, // /home/mvvn8107/fraicheur.ape-com.xyz/wp-content/uploads/2022/03/entreprot-fraicheur-lyonnaise-scaled.jpg
        'url'  => $url, // https://fraicheur.ape-com.xyz/wp-content/uploads/2022/03/entreprot-fraicheur-lyonnaise-scaled.jpg
        'type' => $sizeinfo['mime-type'],
      ];
      //$this->debug('image_data send to convert');$this->debug($image_data);
      $new_image_data = $this->convert_webp( $image_data, $quality ,$debug);

      $new_sizeinfo              = $sizeinfo;
      $new_sizeinfo['file']      = basename($new_image_data['file']);
      $new_sizeinfo['mime-type'] = $new_image_data['type'];

      $new_meta[$size]=$new_sizeinfo;
    }

    //$this->debug('new_meta');$this->debug($new_meta);
    return $new_meta;
  }


  /*function remove_att($attid,$image_data)
  {
    $meta = wp_get_attachment_metadata( $attid );
    $backup_sizes = get_post_meta( $attid, '_wp_attachment_backup_sizes', true );

    $filepath = dirname( $image_data['file'] );
    $result   = wp_delete_attachment_files($attid, $meta, $backup_sizes, $filepath );
    return $result;
  }*/

  function update_db($attid,$new_image_data,$new_meta_data,$debug=0)
  {
    $file=$new_image_data['file'];
    $ext = '.'.pathinfo($file,PATHINFO_EXTENSION );
    $title=basename($file,$ext);
    $mime=$new_image_data['type'];
    $args=[
      'ID'             => $attid,
      'post_mime_type' => $mime,
      'post_title'     => $title,
      'post_name'      => sanitize_title($title),
    ];
    if($debug==0) $r=wp_update_post($args);
    $this->debug( 'wp_update_post ' .($debug?'DEACT':$r).' '.print_r($args,true));

    $new_guid=$new_image_data['url']; // https://fraicheur.ape-com.xyz/wp-content/uploads/2022/03/test-plugin.png
    if($debug==0) {
      global $wpdb;
      $wpdb->update( $wpdb->posts, [ 'guid' =>  $new_guid], ['ID' => $attid] );
    }
    $this->debug( 'update guid ' .($debug?'DEACT':$r).'='.$new_guid);

    if($debug==0) {
      $updated = update_attached_file($attid, $file );
    }
    $this->debug( 'update_attached_file ' .($debug?'DEACT':$r).'='.$file);

    // Generate attachment meta data and create image sub-sizes for images.
    if($debug==0) {
      /*$metadata = wp_generate_attachment_metadata( $attid, $file );
      // delete old intermediate png
      $this->debug('metadata');$this->debug($metadata);*/
      /*
        metadata= Array
        (
            [width] => 1920
            [height] => 785
            [file] => 2022/03/test-plugin.webp
            [sizes] => Array
                (
                    [medium] => Array
                        (
                            [file] => test-plugin-540x221.webp
                            [width] => 540
                            [height] => 221
                            [mime-type] => image/webp
                        )

                    [thumbnail] => Array
                        (
                            [file] => test-plugin-242x144.webp
                            [width] => 242
                            [height] => 144
                            [mime-type] => image/webp
                        )

                    [medium_large] => Array
                        (
                            [file] => test-plugin-768x314.webp
                            [width] => 768
                            [height] => 314
                            [mime-type] => image/webp
                        )

                    [1536x1536] => Array
                        (
                            [file] => test-plugin-1536x628.webp
                            [width] => 1536
                            [height] => 628
                            [mime-type] => image/webp
                        )

                    [feat] => Array
                        (
                            [file] => test-plugin-1440x400.webp
                            [width] => 1440
                            [height] => 400
                            [mime-type] => image/webp
                        )

                    [feat-mobile] => Array
                        (
                            [file] => test-plugin-480x300.webp
                            [width] => 480
                            [height] => 300
                            [mime-type] => image/webp
                        )

                    [carousel] => Array
                        (
                            [file] => test-plugin-704x536.webp
                            [width] => 704
                            [height] => 536
                            [mime-type] => image/webp
                        )

                    [carousel-mobile] => Array
                        (
                            [file] => test-plugin-480x300.webp
                            [width] => 480
                            [height] => 300
                            [mime-type] => image/webp
                        )

                    [banner] => Array
                        (
                            [file] => test-plugin-1046x290.webp
                            [width] => 1046
                            [height] => 290
                            [mime-type] => image/webp
                        )

                    [half] => Array
                        (
                            [file] => test-plugin-540x300.webp
                            [width] => 540
                            [height] => 300
                            [mime-type] => image/webp
                        )

                )

            [image_meta] => Array
                (
                    [aperture] => 0
                    [credit] =>
                    [camera] =>
                    [caption] =>
                    [created_timestamp] => 0
                    [copyright] =>
                    [focal_length] => 0
                    [iso] => 0
                    [shutter_speed] => 0
                    [title] =>
                    [orientation] => 0
                    [keywords] => Array
                        (
                        )

                )

        )
      */
      $r=wp_update_attachment_metadata( $this->post_id, $new_meta_data );
    } else $metadata=false;
    $this->debug( 'wp_update_attachment_metadata ' .($debug?'DEACT':$r));
    return $metadata;
  }
}
