<?php
/**
 * Represents the settings page for the Bob plugin.
 */
class Bob_Settings {
    /**
     * The instance of the Bob_Settings class.
     *
     * @var Bob_Settings
     */
    private static $instance;

    /**
     * The OpenAI settings object.
     *
     * @var Bob_OpenAI_Settings
     */
    private $openai_settings;

    /**
     * The Post Type settings object.
     *
     * @var Bob_Post_Type_Settings
     */
    private $post_type_settings;

    /**
     * Creates a new instance of the Bob_Settings class.
     *
     * @param Bob_OpenAI_Settings    $openai_settings The OpenAI settings object.
     * @param Bob_Post_Type_Settings $post_type_settings The Post Type settings object.
     */
    public function __construct( Bob_OpenAI_Settings $openai_settings, Bob_Post_Type_Settings $post_type_settings ) {
        $this->openai_settings    = $openai_settings;
        $this->post_type_settings = $post_type_settings;

        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * Gets the instance of the Bob_Settings class.
     *
     * @return Bob_Settings The instance of the Bob_Settings class.
     */
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            $openai_settings    = new Bob_OpenAI_Settings();
            $post_type_settings = new Bob_Post_Type_Settings();

            self::$instance = new self( $openai_settings, $post_type_settings );
        }

        return self::$instance;
    }

    /**
     * Adds the settings pages to the WordPress admin menu.
     */
    public function add_settings_page() {
        add_menu_page(
            __( 'Bob Settings', 'bob' ),
            __( 'Bob', 'bob' ),
            'manage_options',
            'bob-settings',
            array( $this, 'render_settings_page' )
        );

        add_submenu_page(
            'bob-settings',
            __( 'OpenAI Settings', 'bob'),
            __( 'OpenAI', 'bob' ),
            'manage_options',
            'bob-openai-settings',
            array( $this->openai_settings, 'render_openai_settings_page' )
        );

        add_submenu_page(
            'bob-settings',
            __( 'Post Type Settings', 'bob' ),
            __( 'Post Type', 'bob' ),
            'manage_options',
            'bob-post-type-settings',
            array( $this->post_type_settings, 'render_post_typesettings_page' )
        );
    }
    
    /**
     * Registers the settings for the OpenAI and Post Type settings pages.
     */
    public function register_settings() {
        $this->openai_settings->register_settings();
        $this->post_type_settings->register_settings();
    }
    
    /**
     * Renders the settings page.
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Bob Settings', 'bob' ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'bob-settings-group' ); ?>
                <?php do_settings_sections( 'bob-settings' ); ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}