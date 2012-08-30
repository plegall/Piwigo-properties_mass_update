<?php
/*
Plugin Name: Properties Mass Update
Version: auto
Description: Update many photo properties at once
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=630
Author: plg
Author URI: http://piwigo.org
*/

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

define('PPMU_PATH', PHPWG_PLUGINS_PATH.basename(dirname(__FILE__)).'/');

/* Plugin admin */
add_event_handler('get_admin_plugin_menu_links', 'ppmu_admin_menu');
function ppmu_admin_menu($menu)
{
  global $page;
  
  array_push(
    $menu,
    array(
      'NAME' => 'Properties Mass Update',
      'URL'  => get_root_url().'admin.php?page=plugin-properties_mass_update'
      )
    );

  return $menu;
}
?>
