<?php
/****
Plugin Name:Post & Term Sector
Plugin URI:
Author: Milton
Author URI:
Description: This is my first plugin
Version: 1.0
License:
License URI:
Text Domain:post-selector
Domain path:/languages/
******/

class Post_Selector{
    public function __construct(){
        add_action('plugins_loaded',array($this,'omb_loaded_text_domain'));
        add_action( 'admin_enqueue_scripts', array($this,'load_admin_assets') );
    }
    public function omb_loaded_text_domain(){
        load_plugin_textdomain( 'post-selector ', false, dirname( __FILE__ ) . "/languages" );
    }
    public function load_admin_assets(){
        wp_enqueue_style( 'select2', plugin_dir_url( __FILE__ ).'assets/css/select2.min.css',null,"1.0" );
        wp_enqueue_script('select2', plugin_dir_url( __FILE__ ).'assets/js/select2.min.js',array('jquery'),true,time());
    }
}
new Post_Selector();