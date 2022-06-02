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

        add_action('admin_menu',array($this,'omb_add_meta_box'));
        add_action('save_post',array($this,'omb_save_metaboxes'));
    }
    public function omb_loaded_text_domain(){
        load_plugin_textdomain( 'post-selector ', false, dirname( __FILE__ ) . "/languages" );
    }
    public function load_admin_assets(){
        wp_enqueue_style( 'select2', plugin_dir_url( __FILE__ ).'assets/css/select2.min.css',null,"1.0" );
        wp_enqueue_script('select2', plugin_dir_url( __FILE__ ).'assets/js/select2.min.js',array('jquery'),true,time());
        wp_enqueue_script('post-selector', plugin_dir_url( __FILE__ ).'assets/js/post-selector.js',array('jquery'),true,time());
    }
    public function omb_add_meta_box(){
        add_meta_box( 
            'post_selector',
            __('Select Posts','omb-metabox'),
            array($this,'omb_display_metabox'),
            'page'
        );
    }
    public function is_sceure($action,$nonce_field,$post_id){
        $nonce=isset($_POST[$nonce_field]) ? $_POST[$nonce_field]:'';
        if($nonce==''){
            return false;
        }
        if(!wp_verify_nonce( $nonce, $action )){
            return false;
        }
        
        if(!current_user_can( 'edit_post',$post_id )){
            return false;
        }
        if(wp_is_post_autosave( $post_id )){
            return false;
        }
        if(wp_is_post_revision( $post_id )){
            return false;
        }
        return true;
    }
    public function omb_save_metaboxes($post_id){
        // if(!$this->is_sceure('omb_selector_post','omb_selector_post_feild',$post_id)){
        //     return $post_id;
        // }
        $selected_post_id=$_POST['posts'];
        if($selected_post_id>=0){
            update_post_meta($post_id,'posts',$selected_post_id);
        }
    }
    public function omb_display_metabox($post){
       
            wp_nonce_field( 'omb_selector_post', 'omb_selector_post_feild' );
            $label=__('Select Posts','omb-metabox');
            $selected_post=get_post_meta( $post->ID, 'posts', true );
            $args=array(
                    'post_type' =>'post',
                    'posts_per_page'=>-1
            );
            $dropdown_list='';
            $_posts=new  WP_Query( $args);
            while($_posts->have_posts()){
                $_posts->the_post();
                $extra='';
                if($selected_post){
                    if(in_array(get_the_ID(),$selected_post)){
                        $extra='selected';
                    }
                }
                
                $dropdown_list .=sprintf("<option %s value='%s'>%s</option>",$extra,get_the_ID(),get_the_title());
            }
            wp_reset_query();
            $metabox.=<<<EOD
            <p>
            <label>{$label}</label>
            <select class="js-example-basic-multiple" name="posts[]" multiple="multiple"  style="width: 80%">
            {$dropdown_list}
            </select>
            EOD;
    
            echo $metabox;
        
    }
}
new Post_Selector();