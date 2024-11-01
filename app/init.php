<?php

/*
 * Plugin Entry Point
 */

class WST_Module_Init {

	static public function WST_Init() {

		self::wst_constants();
		add_action( 'plugins_loaded', array( __CLASS__, 'wst_register_text_domain' ) );
		$files = glob( WST_PLUGIN_ROOT . "app/widgets/*.php" );
		foreach ( $files as $file ) {
			if ( !is_dir( $file ) ) {
				require_once( $file );
			}
		}
		add_action( 'widgets_init', array( 'WST_Widgets', 'register' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wst_dailymotion_medias_front' ) );
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'wst_dailymotion_medias' ) );
		}
	}

	static public function wst_constants() {
		if ( !file_exists( ABSPATH . '/wp-admin/includes/plugin.php' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		define( 'WST_PLUGIN_URI', self::wst_removelastdir( plugins_url( null, __FILE__ ), 1 ) . '/' );
		define( 'WST_PLUGIN_ROOT', self::wst_removelastdir( strtr( plugin_dir_path( __FILE__ ), '\\', '/' ), 2 ) . '/' );
		define( 'WST_PLUGIN_VERSION', '1.0' );
		define( 'PLUGIN_PREFIX', 'WST_' );
		define( 'WST_PLUGIN_DIR', WST_PLUGIN_ROOT . 'app' );
	}

	static public function wst_dailymotion_medias_front() {
		wp_enqueue_style( 'wst_dailymotion_widget_style', WST_PLUGIN_URI . 'app/assets/css/widget_style.css', array(), WST_PLUGIN_VERSION, 'all' );
		wp_register_script( 'wst_popup', WST_PLUGIN_URI . 'app/assets/js/jquery.poptrox.min.js', array(), WST_PLUGIN_VERSION, true );
	}

	static public function wst_removelastdir( $path, $level ) {
		if ( is_int( $level ) && $level > 0 ) {
			$path = preg_replace( '#\/[^/]*$#', '', $path );
			return self::wst_removelastdir( $path, (int) $level - 1 );
		}
		return $path;
	}

	static function wst_register_text_domain() {
		load_plugin_textdomain( 'wp_dailymotion_latest_video', FALSE, WST_PLUGIN_ROOT . '/languages/' );
	}

	static public function wst_dailymotion_medias() {
		$current_screen = get_current_screen();
		if ( _set( $current_screen, 'id' ) == 'widgets' ) {
			echo "\n";
			echo '<script>var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '";</script>';
			echo "\n";
			wp_register_script( 'wst_google_fonts_script', WST_PLUGIN_URI . 'app/assets/js/jquery.fontselect.min.js', array(), WST_PLUGIN_VERSION, true );
			wp_enqueue_script( array( 'jquery', 'wp-color-picker', 'wst_google_fonts_script' ) );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'wst_google_fonts_style', WST_PLUGIN_URI . 'app/assets/css/fontselect.css', array(), WST_PLUGIN_VERSION, 'all' );
		}
	}

}
