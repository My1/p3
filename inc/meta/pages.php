<?php 

if (!defined('ABSPATH')) die;

function pipdig_p3_meta_boxes_page($meta_boxes) {
	$prefix = 'pipdig_meta_';

	// Post meta boxes
	$meta_boxes[] = array(
		'id'       => 'page_options',
		'title'    => __('Extra Page Options', 'p3').' (pipdig)',
		'pages'    => 'page',
		'context'  => 'side',
		'priority' => 'low',
		'fields' => array(
			array(
				'name'		=> __('Hide the page title', 'p3'),
				'id'		=> $prefix . 'hide_page_title',
				'clone'		=> false,
				'type'		=> 'checkbox',
				'std'		=> false
			),
			array(
				'name'		=> __('Hide the website header/logo', 'p3'),
				'id'		=> $prefix . 'hide_page_header',
				'clone'		=> false,
				'type'		=> 'checkbox',
				'std'		=> false
			),
		)
	);
	
	return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'pipdig_p3_meta_boxes_page' );