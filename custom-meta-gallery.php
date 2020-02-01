<?php 
/* 
Plugin Name: Custom Meta Gallery
Plugin URI: https://nikolashaug.com/
Description: An image uploader that allows for multiple images on posts
Author: Niko
Version: 1.0
Author URI: https://nikolashaug.com/
*/

/*
 * Add a meta box
 */
function add_your_fields_meta_box() {
    add_meta_box(
        'your_fields_meta_box',
        'Your Fields',
        'show_your_fields_meta_box',
        'post',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'add_your_fields_meta_box' );

function show_your_fields_meta_box() {
    global $post;
        $meta = get_post_meta( $post->ID, 'your_fields', true ); ?>

        <div class="image-preview">

        <?php $image_ids = explode( ',', $meta['image'] );
                foreach($image_ids as $image_id) {
                    ?>
                    <div class="container-sm">
                        <img src="<?php echo $image_id; ?>" style="width: 250px;">
                    </div>
                    <?php
                }
                    
            ?>

        </div>
        
        

    <input type="hidden" name="your_meta_box_nonce" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>">

    <!-- All fields will go here -->

    <p>
    	<label for="your_fields[image]">Image Upload</label><br>
        <input type="text" name="your_fields[image]" id="your_fields[image]" class="meta-image regular-text" value="<?php echo $meta['image']; ?>">
        <input id="clear-gallery" class="button" type="button"value="<?php esc_html_e('Clear', 'mytheme') ?>"/>
    	<input type="button" class="button image-upload" value="Browse">
    </p>
    <!-- <div class="image-preview"><img src="<?php echo $meta['image']; ?>" style="max-width: 250px;"></div> -->

    <?php }


function mytheme_admin_scripts() {
     
    wp_enqueue_script( 'mytheme-gallery-js', plugin_dir_url( __FILE__ ) . 'meta-gallery.js', array('jquery'), null, true );
     
}
add_action( 'admin_enqueue_scripts','mytheme_admin_scripts' );

function save_your_fields_meta($post_id) {
    // verify nonce
    if(!wp_verify_nonce($_POST['your_meta_box_nonce'], basename(__FILE__) ) ) {
        return $post_id;
    }
    // check autosave
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    // check permissions
    if('page' === $_POST['post_type']) {
        if(!current_user_can( 'edit_page', $post_id )) {
            return $post_id;
        } else if(!current_user_can( 'edit_post', $post_id )) {
            return $post_id;
        }
    }

    $old = get_post_meta( $post_id, 'your_fields', true );
    $new = $_POST['your_fields'];

    if($new && $new !== $old) {
        update_post_meta( $post_id, 'your_fields', $new );
    } else if( '' === $new && $old) {
        delete_post_meta( $post_id, 'your_fields', $old );
    }
}
add_action( 'save_post', 'save_your_fields_meta' );