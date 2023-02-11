<?php

/**
 * Class Bob_Post_Type_Settings
 *
 * Handles the settings page for the Bob Post Type
 */
class Bob_Post_Type_Settings {
    
    /**
	 * Constructor
	 */

    public function __construct() {
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
	 * Registers the settings fields and sections
	 */
    public function register_settings() {
        register_setting( 'bob-post-type-settings-group', 'bob-post-type' );
        register_setting( 'bob-post-type-settings-group', 'bob-taxonomy' );
        
        add_settings_section( 'bob-post-type-section', __( 'Post Type Settings', 'bob' ), array( $this, 'render_post_type_section' ), 'bob-post-type-settings' );
        add_settings_field( 'bob-post-type', __( 'Post Type', 'bob' ), array( $this, 'render_post_type_field' ), 'bob-post-type-settings', 'bob-post-type-section' );
        add_settings_field( 'bob-taxonomy', __( 'Taxonomy', 'bob' ), array( $this, 'render_taxonomy_field' ), 'bob-post-type-settings', 'bob-post-type-section' );
    }

    public function render_post_type_section() {
        echo '<p>' . __( 'Choose the custom post type and taxonomy for your directory site below.', 'bob' ) . '</p>';
    }

    public function render_post_type_field() {
        $post_types = get_post_types( array( 'public' => true ), 'objects' );
        echo '<select name="bob-post-type">';
        foreach ( $post_types as $post_type ) {
            echo '<option value="' . $post_type->name . '" ' . selected( $post_type->name, get_option( 'bob-post-type' ), false ) . '>' . $post_type->labels->name . '</option>';
        }
        echo '</select>';
    }

    public function render_taxonomy_field() {
        $taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
        echo '<select name="bob-taxonomy">';
        foreach ( $taxonomies as $taxonomy ) {
            echo '<option value="' . $taxonomy->name . '" ' . selected( $taxonomy->name, get_option( 'bob-taxonomy' ), false ) . '>' . $taxonomy->labels->name . '</option>';
        }
        echo '</select>';
    }

    /**
    * Renders the settings page
    */
    public function render_post_typesettings_page() {
		?>
        <div class="wrap">
        <h1><?php _e( 'Post Type and Taxonomy Settings', 'bob' ); ?></h1>
        <form method="post" action="options.php">
        <?php settings_fields( 'bob-post-type-settings-group' ); ?>
        <?php do_settings_sections( 'bob-post-type-settings' ); ?>
        <?php submit_button(); ?>
        </form>
        </div>
        <?php
    }
}