<?php 
/* 
Plugin Name: Custom Meta Gallery
Plugin URI: https://nikolashaug.com/
Description: An image uploader that allows for multiple images on posts - includes basic column text input
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
        'Custom Meta Gallery',
        'show_your_fields_meta_box',
        'page',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'add_your_fields_meta_box' );


function show_your_fields_meta_box() {
    global $post;
        $meta = get_post_meta( $post->ID, 'your_fields', true ); ?>

        <!-- Image uploader -->

        <div class="image-preview">

        <?php $image_ids = explode( ',', $meta['image'] );
                foreach($image_ids as $image_id) {
                    ?>
                    <div>
                        <img src="<?php echo $image_id; ?>" style="width: 100%;">
                    </div>
                    <?php
                }
                    
            ?>

        </div>
        
    <input type="hidden" name="your_meta_box_nonce" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>">

    <p>
    	<label for="your_fields[image]">Image Upload</label><br>
        <input type="hidden" name="your_fields[image]" id="your_fields[image]" class="meta-image regular-text" value="<?php if(is_array($meta) && isset($meta['image'])){ echo $meta['image']; }?>" style="width: 100%;">
        <input id="clear-gallery" class="button" type="button"value="<?php esc_html_e('Clear', 'mytheme') ?>"/>
    	<input type="button" class="button image-upload" value="Browse">
    </p>

    <!-- Text input for gallery column widths -->
    <p>
        <label for="your_fields[text]">Column Details:</label>
        <input type="text" name="your_fields[text]" id="your_fields[text]" value="<?php if(is_array($meta) && isset($meta['text'])){ echo $meta['text']; } ?>">
    </p>

    <!-- <p>
    	<label for="your_fields[checkbox]">Checkbox
    		<input type="checkbox" name="your_fields[checkbox]" value="checkbox" <?php if ( $meta['checkbox'] === 'checkbox' ) echo 'checked'; ?>>
    	</label>
    </p> -->


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

/**
 * Add the meta box stylesheet when appropriate
 */
function custom_meta_gallery_styles() {
    global $typenow;
    if($typenow == 'page') {
        wp_enqueue_style( 'custom_meta_gallery_styles', plugin_dir_url(__FILE__) . 'custom-meta-gallery.css' );
    }
}
add_action( 'admin_print_styles', 'custom_meta_gallery_styles' );