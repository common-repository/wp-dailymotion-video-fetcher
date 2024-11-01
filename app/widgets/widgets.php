<?php

if ( !defined( "WST_PLUGIN_DIR" ) )
	die( '!!!' );

class WST_Widgets {

	static protected $_widgets = array(
		'wp_dailymotion',
	);

	static public function register() {
		$_widgets_ = array();
		foreach ( self::$_widgets as $widget ) {
			$_widgets_[WST_PLUGIN_ROOT . 'app/widgets/' . strtolower( $widget ) . '.php'] = $widget;
		}
		$_widgets_ = apply_filters( 'wst_extend_widgets_', $_widgets_ );
		foreach ( $_widgets_ as $path => $register ) {
			require_once( $path );
			$widget_class = 'WST_' . $register . '_Widget';
			register_widget( $widget_class );
		}
	}

}
