<?php
/**
 * Plugin Name: Chat AI Chatbot (CAC)
 * Plugin URI:  https://github.com/iamhassanhafeez/ap-ai-chatbot
 * Description: Floating AI Chatbot widget that proxies chat messages to a configured webhook.
 * Version:     1.0.0
 * Author:      Hassan "Cheeta" Hafeez
 * Author URI:  https://iamhassanhafeez.github.io/portfolio/
 * Text Domain: cac-ai-chat
 * Domain Path: /languages
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CAC_AI_Chatbot' ) ) :

class CAC_AI_Chatbot {

	/**
	 * Option name
	 */
	const OPTION_KEY = 'cac_ai_chat_settings';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->define_constants();
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	private function define_constants() {
		if ( ! defined( 'CAC_PLUGIN_DIR' ) ) {
			define( 'CAC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}
		if ( ! defined( 'CAC_PLUGIN_URL' ) ) {
			define( 'CAC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}
	}

	public function init() {
		load_plugin_textdomain( 'cac-ai-chat', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/****************************
	 * Admin
	 ****************************/
	public function admin_menu() {
		add_options_page(
			__( 'AI Chatbot', 'cac-ai-chat' ),
			__( 'AI Chatbot', 'cac-ai-chat' ),
			'manage_options',
			'cac-ai-chat-settings',
			array( $this, 'settings_page' )
		);
	}

	public function register_settings() {
		register_setting( 'cac_ai_chat_group', self::OPTION_KEY, array( $this, 'sanitize_settings' ) );

add_settings_field(
	'enabled',
	__( 'Enable Widget', 'cac-ai-chat' ),
	array( $this, 'field_enabled' ),
	'cac-ai-chat-settings',
	'cac_main_section'
);

add_settings_field(
	'webhook_url',
	__( 'Webhook URL', 'cac-ai-chat' ),
	array( $this, 'field_webhook_url' ),
	'cac-ai-chat-settings',
	'cac_main_section'
);

add_settings_field(
	'position',
	__( 'Widget Position', 'cac-ai-chat' ),
	array( $this, 'field_position' ),
	'cac-ai-chat-settings',
	'cac_main_section'
);

add_settings_field(
	'vertical_offset',
	__( 'Vertical Offset (px)', 'cac-ai-chat' ),
	array( $this, 'field_vertical_offset' ),
	'cac-ai-chat-settings',
	'cac_main_section'
);

add_settings_field(
	'avatar_collapsed',
	__( 'Collapsed Avatar', 'cac-ai-chat' ),
	array( $this, 'field_avatar_collapsed' ),
	'cac-ai-chat-settings',
	'cac_main_section'
);

add_settings_field(
	'avatar_expanded',
	__( 'Expanded Avatar', 'cac-ai-chat' ),
	array( $this, 'field_avatar_expanded' ),
	'cac-ai-chat-settings',
	'cac_main_section'
);

	}

public function sanitize_settings( $input ) {
	$san = array();

	// Enabled
	$san['enabled'] = isset( $input['enabled'] ) && intval( $input['enabled'] ) === 1 ? 1 : 0;

	if ( isset( $input['webhook_url'] ) ) {
		$san['webhook_url'] = esc_url_raw( trim( $input['webhook_url'] ) );
	}

	$allowed_positions = array( 'right', 'left' );
	$san['position'] = in_array( $input['position'] ?? 'right', $allowed_positions, true ) ? $input['position'] : 'right';

	// vertical_offset now used as BOTTOM offset in px
	$san['vertical_offset'] = intval( $input['vertical_offset'] ?? 120 );
	if ( $san['vertical_offset'] < 0 ) {
		$san['vertical_offset'] = 0;
	}

	$san['avatar_collapsed'] = isset( $input['avatar_collapsed'] ) ? esc_url_raw( $input['avatar_collapsed'] ) : '';
	$san['avatar_expanded']  = isset( $input['avatar_expanded'] ) ? esc_url_raw( $input['avatar_expanded'] ) : '';

	return $san;
}


	/* Settings fields callbacks */
	public function field_webhook_url() {
		$options = get_option( self::OPTION_KEY, array() );
		printf(
			'<input type="url" name="%1$s[webhook_url]" value="%2$s" class="regular-text" placeholder="%3$s" />',
			esc_attr( self::OPTION_KEY ),
			esc_attr( $options['webhook_url'] ?? '' ),
			esc_attr__( 'https://your-webhook.example/api', 'cac-ai-chat' )
		);
		echo '<p class="description">' . esc_html__( 'The plugin will forward messages to this webhook. Keep it private.', 'cac-ai-chat' ) . '</p>';
	}

	public function field_position() {
		$options = get_option( self::OPTION_KEY, array() );
		$val = $options['position'] ?? 'right';
		?>
		<select name="<?php echo esc_attr( self::OPTION_KEY ); ?>[position]">
			<option value="right" <?php selected( $val, 'right' ); ?>><?php esc_html_e( 'Right', 'cac-ai-chat' ); ?></option>
			<option value="left" <?php selected( $val, 'left' ); ?>><?php esc_html_e( 'Left', 'cac-ai-chat' ); ?></option>
		</select>
		<?php
	}

	public function field_vertical_offset() {
		$options = get_option( self::OPTION_KEY, array() );
		$val = intval( $options['vertical_offset'] ?? 120 );
		printf(
			'<input type="number" name="%1$s[vertical_offset]" value="%2$d" min="0" />',
			esc_attr( self::OPTION_KEY ),
			$val
		);
		echo '<p class="description">' . esc_html__( 'px offset from bottom', 'cac-ai-chat' ) . '</p>';
	}

	public function field_avatar_collapsed() {
		$options = get_option( self::OPTION_KEY, array() );
		$value = $options['avatar_collapsed'] ?? '';
		$this->media_field_markup( 'avatar_collapsed', $value );
	}

	public function field_avatar_expanded() {
		$options = get_option( self::OPTION_KEY, array() );
		$value = $options['avatar_expanded'] ?? '';
		$this->media_field_markup( 'avatar_expanded', $value );
	}

	private function media_field_markup( $key, $value ) {
		$option_key = esc_attr( self::OPTION_KEY );
		$id = 'cac_' . $key;
		$img = $value ? '<img src="' . esc_url( $value ) . '" style="max-width:80px;display:block;margin-bottom:6px;border-radius:50%;" />' : '';
		echo sprintf(
			'%1$s<input type="hidden" id="%2$s" name="%3$s[%4$s]" value="%5$s" />',
			$img,
			esc_attr( $id ),
			$option_key,
			esc_attr( $key ),
			esc_attr( $value )
		);
		echo sprintf( '<br/><button type="button" class="button cac-media-upload" data-target="%1$s">%2$s</button>', esc_attr( $id ), esc_html__( 'Select image', 'cac-ai-chat' ) );
		echo ' <button type="button" class="button cac-media-remove" data-target="' . esc_attr( $id ) . '">' . esc_html__( 'Remove', 'cac-ai-chat' ) . '</button>';
	}

	public function settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'AI Chatbot Settings', 'cac-ai-chat' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'cac_ai_chat_group' );
				do_settings_sections( 'cac-ai-chat-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/****************************
	 * Assets
	 ****************************/
public function enqueue_frontend_assets() {
	$options = get_option( self::OPTION_KEY, array() );

	// Bail if widget disabled
	if ( empty( $options['enabled'] ) || intval( $options['enabled'] ) !== 1 ) {
		return;
	}

	wp_enqueue_style( 'cac-frontend-css', CAC_PLUGIN_URL . 'assets/css/frontend.css', array(), '1.0.0' );
	wp_enqueue_script( 'cac-frontend-js', CAC_PLUGIN_URL . 'assets/js/frontend.js', array(), '1.0.0', true );

	$localized = array(
		'rest_url' => esc_url_raw( rest_url( 'ai-chatbot/v1/message' ) ),
		'nonce'    => wp_create_nonce( 'wp_rest' ),
		'settings' => array(
			'position'         => $options['position'] ?? 'right',
			'vertical_offset'  => intval( $options['vertical_offset'] ?? 120 ),
			'avatar_collapsed' => $options['avatar_collapsed'] ?? '',
			'avatar_expanded'  => $options['avatar_expanded'] ?? '',
		),
	);

	wp_localize_script( 'cac-frontend-js', 'CAC_CHAT', $localized );
}


	public function enqueue_admin_assets( $hook ) {
		if ( 'settings_page_cac-ai-chat-settings' !== $hook ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script( 'cac-admin-js', CAC_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_style( 'cac-admin-css', CAC_PLUGIN_URL . 'assets/css/admin.css', array(), '1.0.0' );
	}

	/****************************
	 * REST route - proxy to webhook
	 ****************************/
	public function register_rest_routes() {
		register_rest_route(
			'ai-chatbot/v1',
			'/message',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_message_handler' ),
				'permission_callback' => function () {
					return is_user_logged_in() || true; // we protect with nonce, allow users (or guests) to message
				},
			)
		);
	}

	public function rest_message_handler( WP_REST_Request $request ) {
		$nonce = $request->get_header( 'X-WP-Nonce' );
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new WP_REST_Response( array( 'error' => 'Invalid nonce' ), 403 );
		}

		$params = $request->get_json_params();
		$message = isset( $params['message'] ) ? sanitize_text_field( wp_unslash( $params['message'] ) ) : '';

		$options = get_option( self::OPTION_KEY, array() );
		$webhook = $options['webhook_url'] ?? '';

		if ( empty( $webhook ) ) {
			return new WP_REST_Response( array( 'error' => 'Webhook not configured' ), 500 );
		}

		// Build payload for webhook - you can customise as needed.
		$payload = array(
			'message' => $message,
			'url'     => get_home_url(),
			'ref'     => wp_get_referer(),
			'user_ip' => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
		);

		$args = array(
			'body'        => wp_json_encode( $payload ),
			'headers'     => array(
				'Content-Type' => 'application/json',
			),
			'timeout'     => 20,
			'sslverify'   => true,
		);

		$response = wp_remote_post( $webhook, $args );

		if ( is_wp_error( $response ) ) {
			return new WP_REST_Response( array( 'error' => $response->get_error_message() ), 500 );
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );

		// Try to decode JSON from webhook and forward it; otherwise forward raw body.
		$decoded = json_decode( $body, true );
		if ( JSON_ERROR_NONE === json_last_error() && is_array( $decoded ) ) {
			return new WP_REST_Response( $decoded, $code );
		}

		return new WP_REST_Response( array( 'response' => $body ), $code );
	}

	public function field_enabled() {
	$options = get_option( self::OPTION_KEY, array() );
	$val = isset( $options['enabled'] ) && $options['enabled'] ? 1 : 0;
	printf(
		'<label><input type="checkbox" name="%1$s[enabled]" value="1" %2$s /> %3$s</label>',
		esc_attr( self::OPTION_KEY ),
		checked( 1, $val, false ),
		esc_html__( 'Enable the floating chat widget (toggle on/off)', 'cac-ai-chat' )
	);
}


} // end class

new CAC_AI_Chatbot();
endif;