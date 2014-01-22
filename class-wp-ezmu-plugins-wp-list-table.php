<?php
/*
 * We're going to use WP's WP_List_Table class. 
 *
 * See plugin: Custom List Table Example for more info: http://wordpress.org/plugins/custom-list-table-example/
 */

// No WP? Die! Now!!
if (!defined('ABSPATH')) {
	header( 'HTTP/1.0 403 Forbidden' );
    die();
}

 
if( ! class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
 
if (!class_exists('Class_WP_ezMU_Plugins_WP_List_Table')){

	class Class_WP_ezMU_Plugins_WP_List_Table extends WP_List_Table {
	
		protected $_arr_wp_ezmu_plugins_list;
		protected $_arr_defaults;
		protected $_int_per_page;

		/*
		 * 
		 */
	    public function __construct($arr_defaults, $int_per_page = 200){
			global $status, $page;
			
			$this->_arr_defaults = $arr_defaults;
			$this->_int_per_page = $int_per_page;
					
			//Set parent default
			parent::__construct($arr_defaults['list_table']);
			
		}
		
		// This is how we pass our extenal "data list" (read: array of MU Plugins) into the mix
		public function set_ezmu_plugins($arr_args = NULL){
		
			if ( is_array($arr_args) ){
				$this->_arr_wp_ezmu_plugins_list = $arr_args;
			}
		}
		
		/*
		 * Note: This next bit was copied from the plugin example
		 *
		 * For more detailed insight into how columns are handled, take a look at 
		 * WP_List_Table::single_row_columns()
		 * 
		 * @param array $item A singular item (one full row's worth of data)
		 * @param array $column_name The name/slug of the column to be processed
		 * @return string Text or HTML to be placed inside the column <td>
		 */
		public function column_default($arr_item, $str_column_name){
		
			$arr_defaults = $this->_arr_defaults;
			
			switch($str_column_name){
			
				case 'require_order':
				
					$str_to_echo = '<p>';
					$str_to_echo .= $arr_item['require_order'];
					$str_to_echo .= '</p>';

					return $str_to_echo;
				
				break;
				
				case 'plugin':
				
				
					$str_to_echo = '<strong>';
					$str_to_echo .= $arr_item['name'];
					$str_to_echo .= '</p></strong>';					
					return $str_to_echo;
				break;
				
				case 'status_network':
				
					$str_to_echo = '<p>';
					$str_to_echo .= '<strong>' . $arr_item['active_network'] . '</strong>';
					$str_to_echo .= '</p>';
					return $str_to_echo;
				
				break;
				
				case 'status_site':
					
					$str_to_echo = '<p>';
					$str_to_echo .= '<strong>' . $arr_item['active_site'] . '</strong>'; 
					$str_to_echo .= '</p>';
					return $str_to_echo;
				
				break;
				
				
				case 'details':
				
					$str_to_echo_link = '&mdash; ' . $arr_defaults['link'] . ': ' . $arr_defaults['n_a'];
					if ( !empty($arr_item['link']) ){
						$arr_item['link'] = esc_url($arr_item['link']);
						$str_to_echo_link = '<a href="' . trim($arr_item['link']) . '" target="_blank">' . '&mdash;' .  $arr_defaults['link_plugin_page'] . '</a>';
					}

					$str_to_echo = '<p>';
					$str_to_echo .= '&mdash; <strong>' . $arr_defaults['version'] . ': ' . $arr_item['version'] . '</strong><br />' . $str_to_echo_link .'</br>';
					$str_to_echo .= '&mdash; ' . $arr_defaults['require_once'] . ': ' .  $arr_item['require_once_result'] . '</p>';
				
					$str_to_echo .= '<p>';
					$str_to_echo .= '&mdash; <strong>' . $arr_defaults['description'] . ': </strong>' . $arr_item['description'];
					$str_to_echo .= '</p>';
					$str_to_echo .= '<p>';
					$str_to_echo .= '&mdash; ' . $arr_defaults['notes'] . ': ' . $arr_item['notes'];
					$str_to_echo .= '</p>';
					
					return $str_to_echo;
					
				break;
				default:
					/*
					 * This should pretty much never happen. But just in case show the whole array for troubleshooting purposes
					 */
					return 'Error: ' . print_r($arr_item,true); 
			}
		}
		
		public function get_columns(){
		
			$arr_defaults = $this->_arr_defaults;
		
			$arr_columns = array(
				'require_order'		=> $arr_defaults['columns']['require_order'],
				'plugin'			=> $arr_defaults['columns']['plugin'],
				'status_network'	=> $arr_defaults['columns']['status_network'],
				'status_site'		=> $arr_defaults['columns']['status_site'],
				'details'			=> $arr_defaults['columns']['details'],
			);	
			return $arr_columns;
		}


		/** 
		 * OPTIONAL 
		 *
		 * If you want one or more columns to be sortable (ASC/DESC toggle), 
		 * you will need to register it here. This should return an array where the 
		 * key is the column that needs to be sortable, and the value is db column to 
		 * sort by. Often, the key and value will be the same, but this is not always
		 * the case (as the value is a column name from the database, not the list table).
		 * 
		 * This method merely defines which columns should be sortable and makes them
		 * clickable - it does not handle the actual sorting. You still need to detect
		 * the ORDERBY and ORDER querystring variables within prepare_items() and sort
		 * your data accordingly (usually by modifying your query).
		 * 
		 * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
		 **************************************************************************/
		function get_sortable_columns() {
			$arr_sortable_columns = array(
										'require_order'			=> array('require_order', true),     // true means it's already sorted
										'plugin'		   		=> array('name', true),    			 // true means it's already sorted
										'status_network'		=> array('active_network', true),     // true means it's already sorted
										'status_site'			=> array('active_site', true),     // true means it's already sorted
									);
			return $arr_sortable_columns;
		}
	
		/** 
		 * REQUIRED! 
		 *
		 * This is where you prepare your data for display. This method will
		 * usually be used to query the database, sort and filter the data, and generally
		 * get it ready to be displayed. At a minimum, we should set $this->items and
		 * $this->set_pagination_args(), although the following properties and methods
		 * are frequently interacted with here...
		 * 
		 * @global WPDB $wpdb
		 * @uses $this->_column_headers
		 * @uses $this->items
		 * @uses $this->get_columns()
		 * @uses $this->get_sortable_columns()
		 * @uses $this->get_pagenum()
		 * @uses $this->set_pagination_args()
		 */
		public function prepare_items() {

			//This is used only if making any database queries
			// global $wpdb; 

			/*
			 * First, lets decide how many records per page to show
			 */
			$per_page = $this->_int_per_page;
			
			
			/*
			 * REQUIRED
			 * Now we need to define our column headers. This includes a complete array of columns to be displayed (slugs & 
			 * titles), a list of columns to keep hidden, and a list of columns that are sortable. Each of these can be defined in 
			 * another method (as we've done here) before being used to build the value for our _column_headers property.
			 */
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			
			/*
			 * REQUIRED. 
			 * Finally, we build an array to be used by the class for column headers. The $this->_column_headers property takes 
			 * an array which contains 3 other arrays. One for all columns, one for hidden columns, and one for sortable columns.
			 */
			$this->_column_headers = array($columns, $hidden, $sortable);
			
			
			/*
			 * Optional. You can handle your bulk actions however you see fit. In this
			 * case, we'll handle them within our package just to keep things clean.
			 */
			//$this->process_bulk_action();
			
			
			/**
			 * Instead of querying a database, we're going to fetch the example data
			 * property we created for use in this plugin. This makes this example 
			 * package slightly different than one you might build on your own. In 
			 * this example, we'll be using array manipulation to sort and paginate 
			 * our data. In a real-world implementation, you will probably want to 
			 * use sort and pagination data to build a custom query instead, as you'll
			 * be able to use your precisely-queried data immediately.
			 */

			$data = $this->_arr_wp_ezmu_plugins_list;			
			
			/**
			 * This checks for sorting input and sorts the data in our array accordingly.
			 * 
			 * In a real-world situation involving a database, you would probably want 
			 * to handle sorting by passing the 'orderby' and 'order' values directly 
			 * to a custom query. The returned data will be pre-sorted, and this array
			 * sorting technique would be unnecessary.
			 */
			function usort_reorder($a,$b){
				$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'require_order'; //If no sort, default to title
				$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
				$result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
				return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
			}
			usort($data, 'usort_reorder');
			
			
			/***********************************************************************
			 * ---------------------------------------------------------------------
			 * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
			 * 
			 * In a real-world situation, this is where you would place your query.
			 * 
			 * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
			 * ---------------------------------------------------------------------
			 **********************************************************************/
			
					
			/**
			 * REQUIRED for pagination. 
			 & Let's figure out what page the user is currently 
			 * looking at. We'll need this later, so you should always include it in 
			 * your own package classes.
			 */
			$current_page = $this->get_pagenum();
			
			/**
			 * REQUIRED for pagination. 
			 * Let's check how many items are in our data array. 
			 * In real-world use, this would be the total number of items in your database, 
			 * without filtering. We'll need this later, so you should always include it 
			 * in your own package classes.
			 */
			$total_items = count($data);
			
			
			/**
			 * The WP_List_Table class does not handle pagination for us, so we need
			 * to ensure that the data is trimmed to only the current page. We can use
			 * array_slice() to 
			 */
			$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
			
			
			
			/**
			 * REQUIRED. Now we can add our *sorted* data to the items property, where 
			 * it can be used by the rest of the class.
			 */
			$this->items = $data;
			
			
			/**
			 * REQUIRED. We also have to register our pagination options & calculations.
			 */
			$this->set_pagination_args( 
										array(
											'total_items' => $total_items,                  //WE have to calculate the total number of items
											'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
											'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
										) 
									);
		}
	}
}

?>