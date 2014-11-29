<?php
/*
Plugin Name: WPezMU-Plugins
Plugin URI: https://github.com/WPezPlugins/WPezMUPlugins
Description: The standard mu-plugins folder is "unstructured". WP ezMU-Plugins approximates something closer to the traditional WP plugins folder structure and UI. It also enables you to control load order. 
Version: 0.5.1
Author: Mark Simchock for Alchemy United (http://AlchemyUnited.com)
Author URI: http://ChiefAlchemist.com?TODO
License: The MIT License (MIT) - http://opensource.org/licenses/MIT
*/

/*
 * Dependencies:
 */

 
// No WP? Die! Now!!
if (!defined('ABSPATH')) {
	header( 'HTTP/1.0 403 Forbidden' );
    die();
}

if( ! class_exists('Class_WP_ezMU_Plugins')){
    require_once( 'wp-ezmu-plugins/class-wp-ezmu-plugins.php' );
}

/**
 * Info: http://codex.wordpress.org/Must_Use_Plugins
 *
 * Note: Plugins will load in the order listed. 
 */
if ( !class_exists('WP_ezMU_Plugins')) {
	class WP_ezMU_Plugins extends Class_WP_ezMU_Plugins{
	
		/*
		 * * IMPORTANT * *
		 * The path for the require_once is relative to the Class_WP_ezMU_Plugins. Therefore, you'll probably have to prefix your pathes with '/../'
		 * * * * * * * * *
		 */
		protected function wp_ezmu_plugins_list_master(){

			$arr_return = array(
													
								'wp-ezclasses-master-singleton'		=> array(
																			'active'		=> true,
																			'require_order'	=> '1',
																			'exclude_from'	=> array(), // by blog_id
																			'name'			=> 'WP ezClasses Master Singleton',
																			'version'		=> '0.5.0',
																			'link'			=> NULL,
																			'require_once'	=> '/../wp-ezclasses-master-singleton/class-wp-ezclasses-master-singleton.php',
																			'description'	=> 'The WP ezClasses extends this class. Load it now and forget about it.',
																			'notes'			=> NULL,
																		),
																		
								'wp-ezclasses-autoload'				=> array(
																			'active'		=> true,
																			'require_order'	=> '2',
																			'exclude_from'	=> array(), // by blog_id
																			'name'			=> 'WP ezClasses (Autoload)',
																			'version'		=> '0.5.0',
																			'link'			=> NULL,
																			'require_once'	=> '/../wp-ezclasses-autoload/wp-ezclasses-autoload.php',
																			'description'	=> 'WP ezClasses - An OOP based framework for WP developers.',
																			'notes'			=> '** IMPORTANT ** This plugin must load first.',
																		),																		
																		
								'ez-read-only-options'				=> array(
																			'active'		=> true,
																			'require_order'	=> '3',
																			'exclude_from'	=> array(), // by blog_id
																			'name'			=> 'WP ezGLOBALS',
																			'version'		=> '0.5.0',
																			'link'			=> NULL,
																			'require_once'	=> '/../wp-ezglobals/class-wp-ezglobals-network.php',
																			'description'	=> 'A network / site (*not* theme) level repository for key network and site options (read: properties and methods).',
																			'notes'			=> NULL,
																		),
																			
							);
							
			return $arr_return;
		}
		
	} // close class
} // close if class_exists

$obj_get_instance_wp_ezmu_plugins = new WP_ezMU_Plugins();

?>