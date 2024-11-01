<?php
if ( !defined( "WST_PLUGIN_DIR" ) )
	die( '!!!' );

class WST_wp_dailymotion_Widget extends WP_Widget {

	static public $counter = 0;
	static public $video_thumb_size = array(
		'thumbnail_60_url' => '60',
		'thumbnail_120_url' => '120',
		'thumbnail_180_url' => '180',
		'thumbnail_240_url' => '240',
	);

	public function WST_wp_dailymotion_Widget() {
		$widget_ops = array(
			'description' => __( 'show any usert latest video from Dailymotion', 'wp_dailymotion_latest_video' )
		);
		$control_ops = array(
			'width' => 250,
			'height' => 350,
			'id_base' => 'wst_wp_dailymotion'
		);
		parent::__construct( 'wst_wp_dailymotion', sprintf( __( 'Dailymotion Videos - %1$s', 'wp_dailymotion_latest_video' ), ucfirst( str_replace( '_woo', '', 'WPDigger' ) ) ), $widget_ops, $control_ops );
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$defaults = array( 'title' => __( 'Dailymotion Videos', 'wp_dailymotion_latest_video' ), 'limit' => '4', 'username' => 'dailymotion', 'v_title_size' => 14, 'v_title_clr' => '#FFFFFF', 'v_title_bg_clr' => '#000000', 'title_font' => '', 'fancybox' => 'true', 'title_overlap' => 'false', 'img_size' => 180, 'title_limit' => 200 );
		$instance = wp_parse_args( (array) $instance, $defaults );

		if ( _set( $instance, 'title' ) ) {
			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : _set( $instance, 'title' ), $instance, $this->id_base );
		}

		echo wp_kses( $before_widget, true );
		echo wp_kses( $before_title, true );
		echo wp_kses( $title, true );
		echo wp_kses( $after_title, true );

		$user = _set( $instance, 'username' );
		$limit = _set( $instance, 'limit' );
		$filds = apply_filters( 'wst_dailymotion_fields', array( 'id', 'allow_embed', 'embed_url', 'title', _set( $instance, 'img_size' ) ) );
		$dailymotion_string = implode( ',', $filds );
		$api = "https://api.dailymotion.com/user/$user/videos?limit={$limit}&fields=$dailymotion_string";
		$response = wp_remote_get( $api );
		if ( _set( $instance, 'fancybox' ) == 'true' ) {
			wp_enqueue_script( array( 'jquery', 'wst_popup' ) );
			$popup = 'popup_box';
		} else {
			$popup = '';
		}
		if ( !is_wp_error( $response ) ) {
			$videos = wp_remote_retrieve_body( $response );
		} else {
			$videos = '';
		}
		if ( _set( $instance, 'title_font' ) != '' ) {
			wp_enqueue_style( 'wst_google_font' . $this->id, 'http://fonts.googleapis.com/css?family=' . _set( $instance, 'title_font' ) );
		}


		if ( $videos ) {
			$videos = json_decode( $videos );
			$attr = array();
			$title_bg = (_set( $instance, 'v_title_bg_clr' )) ? 'style=opacity:0.9;background:' . _set( $instance, 'v_title_bg_clr' ) . '' : '';

			if ( _set( $instance, 'v_title_clr' ) != '' ) {
				$attr['color'] = _set( $instance, 'v_title_clr' );
			}
			if ( _set( $instance, 'v_title_size' ) != '' ) {
				$attr['font-size'] = _set( $instance, 'v_title_size' ) . 'px';
			}
			if ( _set( $instance, 'title_font' ) != '' ) {
				$attr['font-family'] = _set( $instance, 'title_font' );
			}
			if ( _set( $instance, 'title_overlap' ) == 'true' ) {
				?>
				<style>
					.flip-card .strip {
						position: absolute;

						-moz-transform: translate(-0%, -100%);
						-o-transform: translate(-0%, -100%);
						-ms-transform: translate(-0%, -100%);
						-webkit-transform: translate(-0%, -100%);
						transform: translate(-0%, -100%);
					}
				</style>
				<?php
			}
			$style = '';
			if ( !empty( $attr ) ) {
				foreach ( $attr as $k => $v ) {
					$style .= $k . ':' . $v . ' !important;';
				}
			}
			$title_class = (!empty( $attr )) ? 'class=wst_title_custom_style' . $this->id : '';
			foreach ( $videos->list as $video ) {
				if ( $video->allow_embed ) {
					$video_url = 'http://dailymotion.com/' . $video->id;
					$src = _set( $instance, 'img_size' );
					$img_src = $video->{$src};
					?>
					<div class="flip-card">
						<div class="card-img <?php echo esc_attr( $popup ) ?>">
							<img src="<?php echo esc_url( $img_src ) ?>" height='<?php echo esc_attr( self::$video_thumb_size[_set( $instance, 'img_size' )] ) ?>' alt="" />
							<a target="_blank" href="<?php echo esc_url( $video_url ) ?>" title="<?php echo esc_attr( $video->title ) ?>">
								<img src="<?php echo esc_url( WST_PLUGIN_URI ) ?>app/assets/css/icon.png" alt="" />
							</a>
						</div>
						<div class="strip" <?php echo esc_attr( $title_bg ) ?>>
							<h2 style="<?php echo esc_attr( $style ) ?>" title="<?php echo esc_attr( $video->title ) ?>">
								<?php
								if ( _set( $instance, 'title_limit' ) != '' ) {
									echo esc_html( substr( $video->title, 0, _set( $instance, 'title_limit' ) ) );
								} else {
									echo esc_html( $video->title );
								}
								?>
							</h2>
						</div>
					</div>
					<?php
				}
			}
			if ( _set( $instance, 'fancybox' ) == 'true' ):
				?>
				<script type="text/javascript">
				    jQuery(document).ready(function ($) {
				        $('.popup_box').poptrox({
				            usePopupCaption: false
				        });
				    });
				</script>
				<?php
			endif;
		}

		echo wp_kses( $after_widget, true );
	}

	/* Store */

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['limit'] = $new_instance['limit'];
		$instance['username'] = $new_instance['username'];
		$instance['v_title_size'] = $new_instance['v_title_size'];
		$instance['v_title_clr'] = $new_instance['v_title_clr'];
		$instance['v_title_bg_clr'] = $new_instance['v_title_bg_clr'];
		$instance['title_font'] = $new_instance['title_font'];
		$instance['title_overlap'] = $new_instance['title_overlap'];
		$instance['fancybox'] = $new_instance['fancybox'];
		$instance['img_size'] = $new_instance['img_size'];
		$instance['title_limit'] = $new_instance['title_limit'];

		return $instance;
	}

	/* Settings */

	public function form( $instance ) {
		$defaults = array( 'title' => __( 'Dailymotion Videos', 'wp_dailymotion_latest_video' ), 'limit' => '4', 'username' => 'dailymotion', 'v_title_size' => 14, 'v_title_clr' => '#FFFFFF', 'v_title_bg_clr' => '#000000', 'title_font' => '', 'fancybox' => 'true', 'title_overlap' => 'false', 'img_size' => 180, 'title_limit' => 200 );
		$instance = wp_parse_args( (array) $instance, $defaults );
		$opt = array( 'true' => __( 'True', 'one-shop' ), 'false' => __( 'False', 'one-shop' ) );

		echo '<p>';
		echo '<label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '"><strong>' . __( "Title:", 'wp_dailymotion_latest_video' ) . '</strong></label>';
		echo '<input type="text" class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" value="' . esc_attr( _set( $instance, 'title' ) ) . '" />';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . esc_attr( $this->get_field_id( 'limit' ) ) . '"><strong>' . __( "Number Of Show Videos:", 'wp_dailymotion_latest_video' ) . '</strong></label>';
		echo '<input type="number" class="widefat" id="' . esc_attr( $this->get_field_id( 'limit' ) ) . '" name="' . esc_attr( $this->get_field_name( 'limit' ) ) . '" value="' . esc_attr( _set( $instance, 'limit' ) ) . '" />';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . esc_attr( $this->get_field_id( 'username' ) ) . '"><strong>' . __( "User Name:", 'wp_dailymotion_latest_video' ) . '</strong></label>';
		echo '<input type="text" class="widefat" id="' . esc_attr( $this->get_field_id( 'username' ) ) . '" name="' . esc_attr( $this->get_field_name( 'username' ) ) . '" value="' . esc_attr( _set( $instance, 'username' ) ) . '" />';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . esc_attr( $this->get_field_id( 'v_title_size' ) ) . '"><strong>' . __( "Video Title Font Size:", 'wp_dailymotion_latest_video' ) . '</strong></label>';
		echo '<input type="text" class="widefat" id="' . esc_attr( $this->get_field_id( 'v_title_size' ) ) . '" name="' . esc_attr( $this->get_field_name( 'v_title_size' ) ) . '" value="' . esc_attr( _set( $instance, 'v_title_size' ) ) . '" />';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . esc_attr( $this->get_field_id( 'v_title_clr' ) ) . '"><strong>' . __( "Video Title Color:", 'wp_dailymotion_latest_video' ) . '</strong></label>';
		echo '<input type="text" class="widefat" id="' . esc_attr( $this->get_field_id( 'v_title_clr' ) ) . '" name="' . esc_attr( $this->get_field_name( 'v_title_clr' ) ) . '" value="' . esc_attr( _set( $instance, 'v_title_clr' ) ) . '" />';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . esc_attr( $this->get_field_id( 'v_title_bg_clr' ) ) . '"><strong>' . __( "Video Title Background:", 'wp_dailymotion_latest_video' ) . '</strong></label>';
		echo '<input type="text" class="widefat" id="' . esc_attr( $this->get_field_id( 'v_title_bg_clr' ) ) . '" name="' . esc_attr( $this->get_field_name( 'v_title_bg_clr' ) ) . '" value="' . esc_attr( _set( $instance, 'v_title_bg_clr' ) ) . '" />';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . esc_attr( $this->get_field_id( 'title_font' ) ) . '"><strong>' . __( "Video Title Font:", 'wp_dailymotion_latest_video' ) . '</strong></label>';
		echo '<input type="text" class="widefat" id="' . esc_attr( $this->get_field_id( 'title_font' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title_font' ) ) . '" value="' . esc_attr( _set( $instance, 'title_font' ) ) . '" />';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . esc_attr( $this->get_field_id( 'title_limit' ) ) . '"><strong>' . __( "Title Limit:", 'wp_dailymotion_latest_video' ) . '</strong></label>';
		echo '<input type="text" class="widefat" id="' . esc_attr( $this->get_field_id( 'title_limit' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title_limit' ) ) . '" value="' . esc_attr( _set( $instance, 'title_limit' ) ) . '" />';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . esc_attr( $this->get_field_id( 'title_overlap' ) ) . '"><strong>' . __( 'Title Overlap:', 'one-shop' ) . '</strong></label>';
		echo '<select class="widefat" id="' . esc_attr( $this->get_field_id( 'title_overlap' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title_overlap' ) ) . '" >';
		foreach ( $opt as $k => $op ) :
			$selected = ( _set( $instance, 'title_overlap' ) == $k ) ? 'selected="selected"' : '';
			echo '<option value="' . esc_attr( $k ) . '" ' . esc_attr( $selected ) . '>' . esc_attr( $op ) . '</option>';
		endforeach;
		echo '</select>';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . esc_attr( $this->get_field_id( 'fancybox' ) ) . '"><strong>' . __( 'Show Vidoe In PopUp:', 'one-shop' ) . '</strong></label>';
		echo '<select class="widefat" id="' . esc_attr( $this->get_field_id( 'fancybox' ) ) . '" name="' . esc_attr( $this->get_field_name( 'fancybox' ) ) . '" >';
		foreach ( $opt as $k => $op ) :
			$selected = ( _set( $instance, 'fancybox' ) == $k ) ? 'selected="selected"' : '';
			echo '<option value="' . esc_attr( $k ) . '" ' . esc_attr( $selected ) . '>' . esc_attr( $op ) . '</option>';
		endforeach;
		echo '</select>';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . esc_attr( $this->get_field_id( 'img_size' ) ) . '"><strong>' . __( 'Thumbnail Size:', 'one-shop' ) . '</strong></label>';
		echo '<select class="widefat" id="' . esc_attr( $this->get_field_id( 'img_size' ) ) . '" name="' . esc_attr( $this->get_field_name( 'img_size' ) ) . '" >';
		foreach ( self::$video_thumb_size as $k => $op ) :
			$selected = ( _set( $instance, 'img_size' ) == $k ) ? 'selected="selected"' : '';
			echo '<option value="' . esc_attr( $k ) . '" ' . esc_attr( $selected ) . '>' . esc_attr( $op ) . '</option>';
		endforeach;
		echo '</select>';
		echo '</p>';
		?>
		<script type="text/javascript">
		    jQuery(document).ready(function ($) {
		        $(function () {
		            $("#<?php echo esc_js( $this->get_field_id( 'title_font' ) ) ?>").fontselect();
		        });
		        var myOptions = {
		            defaultColor: true,
		            hide: true,
		            palettes: true
		        };
		        jQuery("#<?php echo esc_js( $this->get_field_id( 'v_title_clr' ) ) ?>").wpColorPicker(myOptions);
		        jQuery("#<?php echo esc_js( $this->get_field_id( 'v_title_bg_clr' ) ) ?>").wpColorPicker(myOptions);
		    });
		</script>
		<?php
	}

}
