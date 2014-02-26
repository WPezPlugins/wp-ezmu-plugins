<?php
/*
 * Handles pretty much all the dirty work for the WP ezMU-Plugins plugin. Override as you see fit. 
 */


// No WP? Die! Now!!
if (!defined('ABSPATH')) {
	header( 'HTTP/1.0 403 Forbidden' );
    die();
}


if( ! class_exists('Class_WP_ezMU_Plugins_WP_List_Table')){
    require_once( 'class-wp-ezmu-plugins-wp-list-table.php' );
}

/**
 * 
 * Info: http://codex.wordpress.org/Must_Use_Plugins
 *
 * Note: Plugins will load in the order listed in  wp_ezmu_plugins_list_master() in wp-ezmu-plugins.php. 
 */
if ( !class_exists('Class_WP_ezMU_Plugins')) {
	class Class_WP_ezMU_Plugins {
	
		protected $_arr_wp_ezmu_plugins_list_master;
		protected $_arr_defaults;
	
		public function __construct(){
		
			$this->_arr_defaults = $this->wp_ezmu_plugins_defaults();		
			$this->wp_ezmu_plugins_require_once();
			$this->wp_ezmu_plugins_actions();
		}
		
		/*
		 *
		 */
		public function wp_ezmu_plugins_actions(){
		
			add_action( 'admin_menu', array($this, 'wp_ezmu_plugins_menu_page') );
			add_action( 'network_admin_menu', array($this, 'wp_ezmu_plugins_menu_page') );	
		}

		/*
		 *
		 */
		public function wp_ezmu_plugins_defaults(){
			
			$arr_defaults = array(
								'n_a'					=> 'n/a',
								'error'					=> 'Error',
								'active'				=> 'Active',
								'inactive'				=> 'Inactive',
								'success'				=> 'Success',
								'fail'					=> 'Fail',
								'submenu_page_title'	=> 'MU Plugins List',
								'submenu_menu_title'	=> 'MU Plugins List',
								'submenu_capability'	=> 'manage_options',
								'require_once_warning'	=> 'Warning: Value for require_once is not set or is not a string.',
								'page_info_1'			=> 'Must-Use Plugins for Blog ID: ',
								'page_info_2'			=> '<h3>Note: This list is maintained and updated manually via mu-plugins/wp-ezmu-plugins.php.</h3>',
								'list_table'			=> array(
																'singular'  => 'MU Plugin',		// singular name of the listed records
																'plural'    => 'MU Plugins',    // plural name of the listed records
																'ajax'      => true				// does this table support ajax?
															), 
								'columns'				=> array(
																'require_order'		=> 'Order',
																'plugin'			=> 'Plugin',
																'status_network'	=> 'Status: Network',
																'status_site'		=> 'Status: Site',
																'details'			=> 'Details',	
															),
								'link'					=> 'Link',
								'link_plugin_page'		=> 'Link Plugin Page',
								'version'				=> 'Version',
								'require_once'			=> 'require_once',
								'description'			=> 'Description',
								'notes'					=> 'Notes',
							);
							
			return $arr_defaults;
		}		
		
		/*
		 * This is where the magic happens
		 */
		protected function wp_ezmu_plugins_require_once(){
				
			$arr_defaults = $this->_arr_defaults;
		
			$int_get_current_blog_id = get_current_blog_id();
			$arr_wp_ezmu_plugins_list_master = $this->wp_ezmu_plugins_list_master();
			
			foreach ($arr_wp_ezmu_plugins_list_master as $str_key => $arr_value){
			
				$bool_require_result = false;
				
				$str_status = $arr_defaults['inactive'];
				$str_exclude = $arr_defaults['n_a'];
				$str_require_once = $arr_defaults['n_a'];
				if ( isset($arr_value['active']) && $arr_value['active'] === true) {
				
					$str_status = $arr_defaults['active'];
					
					if ( isset($arr_value['require_once']) && is_string($arr_value['require_once']) ){
						
						if ( isset($arr_value['exclude_from']) && is_array($arr_value['exclude_from']) && !in_array($int_get_current_blog_id, $arr_value['exclude_from']) ){
							$str_exclude = $arr_defaults['active'];
							$str_require_once = $arr_defaults['error'] . ': ' . dirname(__FILE__) . '/' . $arr_value['require_once'] . ' does not exist';
							if (@file_exists( dirname(__FILE__) . '/' . $arr_value['require_once']) ){
								$bool_require_result = require(dirname(__FILE__) . '/' . $arr_value['require_once']);
								if ( $bool_require_result == true ) {
									$str_require_once = $arr_defaults['success'];
								} else {
									$str_require_once = $arr_defaults['fail'];
								}
							} 
							
						} elseif ( in_array($int_get_current_blog_id, $arr_value['exclude_from']) ){
							$str_exclude = $arr_defaults['inactive'];
						}
					} else {
						$str_require_once = $arr_defaults['require_once_warning']; 
					}
				}
				$arr_wp_ezmu_plugins_list_master[$str_key]['active_network'] = $str_status;
				$arr_wp_ezmu_plugins_list_master[$str_key]['active_site'] = $str_exclude;
				$arr_wp_ezmu_plugins_list_master[$str_key]['require_once_result'] = $str_require_once;
			}

			// now that the mu plug list has been processeed and the appropriate values set, pass that updated list to WP_List_Table
			// Note: Apparently (read:  further inverstigation = TODO) WP_List_Table renders in reverse order. 
			$this->_arr_wp_ezmu_plugins_list_master = array_reverse($arr_wp_ezmu_plugins_list_master);
			
			return array('status' => true);
		}
			
		
		/*
		 *
		 */
		public function wp_ezmu_plugins_menu_page(){	
			/*
			 * http://codex.wordpress.org/Function_Reference/add_menu_page
			 *
			 * add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
			 */
			 
			$arr_defaults = $this->_arr_defaults;

			$str_settings = add_submenu_page( 'plugins.php', $arr_defaults['submenu_page_title'], $arr_defaults['submenu_menu_title'],  $arr_defaults['submenu_capability'], '', array($this, 'wp_ezmu_plugins_page'));
			
			// we only want the js to enqueue on this page (and no others)
			add_action('load-' . $str_settings, array($this,'wp_ezmu_plugins_wp_enqueue'));	
		}
		
		/*
		 *
		 */
		public function wp_ezmu_plugins_wp_enqueue(){
		
			wp_enqueue_style('wp_ezmu_plugins_admin',   WP_CONTENT_URL.'/mu-plugins/wp-ezmu-plugins/css/wp-ezmu-plugins-admin.css', array(), '0.5.0', 'all');
		}

		/*
		 *
		 */
		public function wp_ezmu_plugins_page(){
		
			$arr_defaults = $this->_arr_defaults;
		
	
			$str_to_echo = '<div class="wrap">';
			$str_to_echo .= '<div id="icon-plugins" class="icon32"><br /></div><h2>' . $arr_defaults['page_info_1'] . get_current_blog_id() . '</h2>';
			$str_to_echo .= $arr_defaults['page_info_2'];
			$str_to_echo .= '</div>';
			
			echo $str_to_echo;
			
			// init the WP List Table child
			$obj_mu_plugins_wp_list_table = new Class_WP_ezMU_Plugins_WP_List_Table($arr_defaults);
			
			// get the (array) list of mu plugins (above)
			$obj_mu_plugins_wp_list_table->set_ezmu_plugins($this->_arr_wp_ezmu_plugins_list_master);
			
			// WP List Table method():  prepare_items()
			$obj_mu_plugins_wp_list_table->prepare_items();

			// WP List Table method()" display()
			$obj_mu_plugins_wp_list_table->display();
			
		}

	} // close class
} // close if class_exists

?>