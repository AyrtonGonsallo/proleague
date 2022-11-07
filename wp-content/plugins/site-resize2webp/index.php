<?php
/*
Plugin Name: site resize 2 webp
Description: resize image when uploaded and convert to webp
Version: 1.2
Author: Digitallin
*/

/*
TODO : bulk action from media library
1.2 : traite les images existantes depuis la mediatheque
1.1 : gestion de la qualité+lossless, bug sauvegarde settings
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
if(!class_exists('plugin_base')) {
  if(file_exists(WP_PLUGIN_DIR.'/site-framework/class.plugin_base.php')) {
    require_once  WP_PLUGIN_DIR.'/site-framework/class.plugin_base.php';
  }
}

if(class_exists('plugin_base') ) {
class site_resize2webp extends plugin_base{

  public $debug=0;

  function __construct()
  {
    $domain = parse_url(site_url(),PHP_URL_HOST);
    $this->option_name = $this->transient_name = $this->classname='site_resize2webp';
    $this->def_options=array(
      'debug' => array (
        'active'        => $this->debug,
        'mail_dest'     => 'zetoun.17@gmail.com',
        'mail_from'     => 'site_resize2webp <noreply@'.$domain.'>',
        'user_can_view' => 'contact@webimedia.fr',
      ),
      'max_width'    =>1920,
      'max_height'   =>1080,
      'quality'      =>80,
      'png_lossless' =>true, // php >=8.1
      'on_upload'    =>true,
    );

    $this->init(__FILE__); // init debug,plugin_dir_path, options


    if(is_admin())
    {
      require_once($this->plugin_dir_path.'admin/admin.php');
      $admin=new site_resize2webp_admin($this);
    }

    register_activation_hook( __FILE__, [$this,'activate']);

  }

  public function get_options()
  {
    if(!isset($this->options))
    {
      $this->init_options();
    }
    // limite qualité entre 0 et 100
    $q=$this->options['quality'];
    $q=min(100,$q);
    $this->options['quality']=max(0,$q);
    return $this->options;
  }

  public function quality($mime)
  {
    $options = $this->get_options();
    $quality = $options['quality'];

    // si png && png_lossless dispo sur php 8.1+
    if($mime=='image/png' && defined('IMG_WEBP_LOSSLESS')) {
      // si lossless activé par l'utilisateur
      if($options['png_lossless']) $quality=IMG_WEBP_LOSSLESS;
    }
    return $quality;
  }
}
global $site_resize2webp;
$site_resize2webp= new site_resize2webp();
}
else {
  add_action( 'admin_notices', function () {
    $class = 'notice notice-error';
    $message = __( 'Error: missing class plugin_base. copy site-framework to plugins dir', 'site_resize2webp' );

    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
  });
}