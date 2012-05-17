<?php
class admin_page_class
{	
	static function generate_admin_page()
	{	
		include('admin_page.php');
	}	
	static function add_menu_item()
	{
		add_submenu_page(
		'options-general.php',
		'wp-api',
		'wp-api',
		'manage_options',
		'wp-api',
		'admin_page_class::generate_admin_page'
		 );
	}
	static function jquery_init() 
	{
		if (is_admin()) 
		{
			wp_deregister_script('jquery');
			wp_register_script('jquery', 'http://code.jquery.com/jquery-1.7.2.js');
			wp_enqueue_script('jquery');
		}
	}
	static function admin_jquery()
	{
		$url = network_site_url('/');
		$dir = $url.'wp-content/plugins/wp-api/includes/admin_jquery.js';
		if (is_admin()) 
		{
			wp_deregister_script('admin_jquery');
			wp_register_script('admin_jquery', $dir, false, '1.3.2');
			wp_enqueue_script('admin_jquery');
		}
	}
}
?>