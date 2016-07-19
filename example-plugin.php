<?php
/**
 * @package Background Process
 * @version 0.1
 */
/*
Plugin Name: Background Process
Plugin URI: http://techslides.com/
Description: Example Plugin that uses WP Background Processing to queue background tasks
Version: 0.1
Author URI: http://techslides.com/
*/

//https://codex.wordpress.org/Plugin_API/Action_Reference/plugins_loaded
add_action( 'plugins_loaded', 'saas_init' );

function saas_init() {
	//require_once plugin_dir_path( __FILE__ ) . 'class-example-process.php';
	class WP_Example_Process extends WP_Background_Process {

		//use WP_Example_Logger;

		/**
		 * @var string
		 */
		protected $action = 'example_process';

		/**
		 * Task
		 *
		 * Override this method to perform any actions required on each
		 * queue item. Return the modified item for further processing
		 * in the next pass through. Or, return false to remove the
		 * item from the queue.
		 *
		 * @param mixed $item Queue item to iterate over
		 *
		 * @return mixed
		 */
		protected function task( $item ) {
			backgroundProcess($item);
			return false;
		}

		/**
		 * Complete
		 *
		 * Override if applicable, but ensure that the below actions are
		 * performed, or, call parent::complete().
		 */
		protected function complete() {
			parent::complete();

			// Show notice to user or perform some other arbitrary task...
		}

	}
	$process_all = new WP_Example_Process();
}



//function to be called in the background: referenced in protected task function above
function backgroundProcess($str) {
  error_log($str);
  sleep(20);
  file_put_contents('/Users/iwo/Sites/wordpress2/wp-content/plugins/background-process/log.txt', $str.PHP_EOL,FILE_APPEND);
}

//function to listen to ajax call, add to queue, and dispatch process
add_action( 'wp_ajax_admin_test', 'prefix_ajax_admin_test' );
function prefix_ajax_admin_test() {
  $data = $_POST['data'];
  //echo $data; //test
  $process_all = new WP_Example_Process();
  $process_all->push_to_queue( $data );
  $process_all->push_to_queue( $data );
  $process_all->save()->dispatch();
  echo "success"; //return something
  wp_die();
}


//initiate ajax call from front-end in js console
//jQuery.ajax({type: "POST",url: ajaxurl,data: {'action': 'admin_test','data': 'go pokeman go'},success: function(data) { console.log(data); }});