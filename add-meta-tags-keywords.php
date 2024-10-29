<?php
/**
 * Plugin Name: Add Meta Tag Keywords
 * Description: Add Meta Tag keywords for posts, pages, custom posts.
 * Version: 1.0.3
 * Author: Hema Rawat
 * Author URI: https://www.epiphanyinfotech.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

function AMTK_register() {

    $post_type_arr = array('post','page');

    global $wpdb;
    $prefix_post = $wpdb->prefix.'posts';
    $post_type = $wpdb->get_results("SELECT DISTINCT post_type FROM ".$prefix_post." WHERE post_type!='revision'");

    $singlearray = [];

    foreach($post_type as $key => $val1){
        foreach($val1 as $meta_post_type){
            $singlearray[] = $meta_post_type;
        }
    }
   
    $all_post_type_arr = !empty($singlearray) ? $singlearray : $post_type_arr;      

    add_meta_box( 
        'mb-post-id',
        'Add Meta Tag Keywords',
        'AMTK_display_callback',
        $all_post_type_arr,         
        'side'
    );
    
}
add_action( 'add_meta_boxes', 'AMTK_register' );


function AMTK_display_callback() { 
?>

<div class="metabox_box components-form-token-field">          
    <p class="meta-options metabox_field"> 
        <input id="hrd_post_meta_tag_keywords" type="text" name="hrd_post_meta_tag_keywords" value="<?php echo esc_attr(get_post_meta(get_the_ID(),'hrd_post_meta_tag_keywords',true));?>" readonly/>
        <br/>
        <p>(Press "," or "↵" to create a tag.)</p>
        <p>
            <?php

            $hrd_checkbox_val = get_post_meta(get_the_ID(),'hrd_checkbox',true);


            //We do not want to show the option to select tags for post type 'pages'
            if('page' != get_post_type()){
            ?>
                <input type="checkbox" <?php if(!empty($hrd_checkbox_val)){ ?> checked <?php } ?>name="hrd_checkbox" id="hrd_checkbox" value="1">Check this to use tags as keywords
            <?php
            }
            ?>

        </p>
    </p>           
</div>


<?php

}

function AMTK_save_post() 
{
    if (defined('DOING_AUTOSAVE')&& DOING_AUTOSAVE) return; //We are not going to save when the page is getting autosaved
    
    global $post;
 
    if(isset($_POST['hrd_post_meta_tag_keywords'])){
        $keywords = $_POST['hrd_post_meta_tag_keywords'];
        $checkbox = $_POST['hrd_checkbox'];
        
        update_post_meta( $post->ID, 'hrd_post_meta_tag_keywords', sanitize_text_field($keywords ) );

        update_post_meta( $post->ID, 'hrd_checkbox', sanitize_text_field( $checkbox ) );
    }
}
add_action( 'save_post', 'AMTK_save_post' );



function AMTK_to_head(){
    $postTag = NULL;

    if('page' != get_post_type()){
        $tag_arr = [];
        $posttags = get_the_tags();
        if(!empty($posttags)){
        foreach($posttags as $tag){
            $tag_arr[] = $tag->name;
        }
        }
        $postTag = esc_attr(implode(",", $tag_arr)); 
        $AMTK_checkbox_val = get_post_meta(get_the_ID(),'hrd_checkbox',true);
        $meta_keywords_str = get_post_meta(get_the_ID(),'hrd_post_meta_tag_keywords', true);

        if(!empty($AMTK_checkbox_val)){

            if(!empty($postTag)){
                ?>
                <!-- From Add Meta Tag Keywords by Hema Rawat, from Epiphany Infotech -->
                <?php
                echo '<meta name="keywords" content="'.esc_attr($postTag).'">';
            }

        }else{
            if(!empty($meta_keywords_str)){
                ?>
                <!-- From Add Meta Tag Keywords by Hema Rawat, from Epiphany Infotec -->
                <?php
                echo '<meta name="keywords" content="'.esc_attr($meta_keywords_str).'">';   
            }

        }

    }else{

        $meta_keywords_str = get_post_meta(get_the_ID(),'hrd_post_meta_tag_keywords', true);
        if(!empty($meta_keywords_str)){
            ?>
            <!-- From Add Meta Tag Keywords by Hema Rawat, from Epiphany Infotec -->
            <?php
            echo '<meta name="keywords" content="'.esc_attr($meta_keywords_str).'">';
        }
    }

}
add_action('wp_head', 'AMTK_to_head' );


function AMTK_js_script() {
   
    $plugin_data = get_plugin_data( __FILE__ );
    $ver = $plugin_data['Version'];

    wp_enqueue_script(
                   'jquery.amsify.suggestags',
                    plugin_dir_url( __FILE__ ) . 'js/jquery.amsify.suggestags.js', 
                    array('jquery'), 
                    $ver, 
                    false 
                );
 
    wp_enqueue_script(
                   'add_meta_tag_plugin_js',
                    plugin_dir_url( __FILE__ ) . 'js/add_meta_tag_plugin.js', 
                    array('jquery', 'jquery.amsify.suggestags'), 
                    $ver, 
                    false 
                );


}   
add_action('admin_enqueue_scripts', 'AMTK_js_script');

function AMTK_css_style() {

    wp_enqueue_style( 
                 'amsify.suggestags', 
                  plugin_dir_url( __FILE__ ) . 'css/amsify.suggestags.css',
                );
    wp_enqueue_style( 
                 'add.meta.tag.keywords', 
                  plugin_dir_url( __FILE__ ) . 'css/add.meta.tag.keywords.css',
                  array('amsify.suggestags'), 
                );
}

add_action('admin_enqueue_scripts', 'AMTK_css_style');



function AMTK_activate() {
    global $wpdb;
    $prefix_postmeta = $wpdb->prefix.'postmeta';
    $delete_meta_value = $wpdb->get_results("DELETE FROM ".$prefix_postmeta." WHERE meta_key = 'hrd_post_meta_tag_keywords' ");
}
register_activation_hook( __FILE__ , 'AMTK_activate' ); 