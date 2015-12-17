<?php

if (!defined('ABSPATH')) {
	exit;
}

function p3_instagram_fetch() {
	
	$instagram_deets = get_option('pipdig_instagram');
	
	if (!empty($instagram_deets['access_token']) && !empty($instagram_deets['user_id'])) { 
	
		$access_token = strip_tags($instagram_deets['access_token']);
		$userid = absint($instagram_deets['user_id']);
		
		if ( false === ( $result = get_transient( 'p3_instagram_feed' ) )) {
			$url = "https://api.instagram.com/v1/users/".$userid."/media/recent/?access_token=".$access_token."&count=20";
			$result = wp_remote_fopen($url);
			set_transient( 'p3_instagram_feed', $result, 15 * MINUTE_IN_SECONDS );
		}
		
		$result = json_decode($result);
		
		//print_r($result);
		
		for ($i = 0; $i < 19; $i++) {
			if (isset($result->data[$i])) {
				$images[$i] = array (
					'src' => esc_url($result->data[$i]->images->standard_resolution->url),
					'link' => esc_url($result->data[$i]->link),
					'likes' => $result->data[$i]->likes->count,
					'comments' => $result->data[$i]->comments->count,
					'caption' => $result->data[$i]->caption->text,
				);
			}
		}
		
		return $images;
		
	} else {
		return false;
	}
}


if (!function_exists('p3_instagram_footer')) {
	function p3_instagram_footer() {
		
		if (!get_theme_mod('p3_instagram_footer')) {
			return;
		}
		
		$images = p3_instagram_fetch(); // grab images
			
		if ($images) {
			$meta = get_theme_mod('p3_instagram_meta', 1);
			$num = get_theme_mod('p3_instagram_number', 8) - 1; // minus 1 for array
			
		?>
			<div id="p3_instagram_footer">
			<?php for ($x = 0; $x <= $num; $x++) { ?>
				<a href="<?php echo $images[$x]['link']; ?>" id="p3_instagram_post_<?php echo $x; ?>" class="p3_instagram_post" style="background-image:url(<?php echo $images[$x]['src']; ?>);" rel="nofollow" target="_blank">
					<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAfQAAAH0AQMAAADxGE3JAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAADVJREFUeNrtwTEBAAAAwiD7p/ZZDGAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAOX0AAAEidG8rAAAAAElFTkSuQmCC" class="p3_instagram_square" alt=""/>
					<?php if ($meta) { ?><span class="p3_instagram_likes"><i class="fa fa-comment"></i> <?php echo $images[$x]['comments'];?> &nbsp;<i class="fa fa-heart"></i> <?php echo $images[$x]['likes'];?></span><?php } ?>
				</a>
			<?php } ?>
			</div>
			<div class="clearfix"></div>
			<?php
		} else { // no access token or user id, so error for admins:
			if (current_user_can('manage_options')) {
				echo '<p style="text-align:center">Unable to display Instagram feed. Please check your account has been correctly setup on <a href="'.admin_url('admin.php?page=pipdig-instagram').'">this page</a>.</p>';
			}
		}
	}
	add_action('p3_footer_bottom', 'p3_instagram_footer', 99);
}



// customiser
if (!class_exists('pipdig_p3_instagram_Customiser')) {
	class pipdig_p3_instagram_Customiser {
		public static function register ( $wp_customize ) {
			
			$wp_customize->add_section( 'pipdig_p3_instagram_section', 
				array(
					'title' => 'Instagram',
					'description' => sprintf(__('Before enabling these features, you will need to add your Instagram account to <a href="%s">this page</a>.', 'p3'), admin_url( 'admin.php?page=pipdig-instagram' )),
					'capability' => 'edit_theme_options',
					'priority' => 111,
				) 
			);

			
			// header feed
			$wp_customize->add_setting('p3_instagram_header',
				array(
					'default' => 0,
					'sanitize_callback' => 'absint',
					'transport' => 'refresh'
				)
			);
			$wp_customize->add_control(
				'p3_instagram_header',
				array(
					'type' => 'checkbox',
					'label' => __( 'Display feed in the header', 'p3' ),
					'section' => 'pipdig_p3_instagram_section',
				)
			);


			// footer feed
			$wp_customize->add_setting('p3_instagram_footer',
				array(
					'default' => 0,
					'sanitize_callback' => 'absint',
					'transport' => 'refresh'
				)
			);
			$wp_customize->add_control(
				'p3_instagram_footer',
				array(
					'type' => 'checkbox',
					'label' => __( 'Display feed in the footer', 'p3' ),
					'section' => 'pipdig_p3_instagram_section',
				)
			);
			
			
			// Number of images to display in instagram feed
			$wp_customize->add_setting( 'p3_instagram_number', array(
				'default' => 8,
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'absint',
				)
			);
			$wp_customize->add_control( 'p3_instagram_number', array(
				'type' => 'number',
				'label' => __('Number of images to display:', 'p3'),
				'section' => 'pipdig_p3_instagram_section',
				'input_attrs' => array(
					'min' => 4,
					'max' => 10,
					'step' => 1,
					),
				)
			);
			
			
			// show likes/comments on hover
			$wp_customize->add_setting('p3_instagram_meta',
				array(
					'default' => 1,
					'sanitize_callback' => 'absint',
				)
			);
			$wp_customize->add_control(
				'p3_instagram_meta',
				array(
					'type' => 'checkbox',
					'label' => __( 'Display Comments & Likes count on hover', 'p3' ),
					'section' => 'pipdig_p3_instagram_section',
				)
			);


		}
	}
	add_action( 'customize_register' , array( 'pipdig_p3_instagram_Customiser' , 'register' ) );
}
