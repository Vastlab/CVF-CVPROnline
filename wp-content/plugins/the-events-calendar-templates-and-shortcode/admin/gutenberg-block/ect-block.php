<?php

function ect_pro_gutenberg_scripts() {
	$blockPath = '/dist/block.js';
	$stylePath = '/dist/block.css';

	// Enqueue the bundled block JS file
	wp_enqueue_script(
		'ect-block-js',
		plugins_url( $blockPath, __FILE__ ),
		[ 'wp-i18n', 'wp-blocks', 'wp-edit-post', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-plugins', 'wp-edit-post', 'wp-api' ],
		filemtime( plugin_dir_path(__FILE__) . $blockPath )
	);
wp_localize_script( 'ect-block-js', 'ectUrl', ECT_PRO_PLUGIN_URL );

	// Enqueue frontend and editor block styles
	wp_enqueue_style(
		'ect-block-css',
		plugins_url( $stylePath, __FILE__ ),
		'',
		filemtime( plugin_dir_path(__FILE__) . $stylePath )
	);
	
}

// Hook scripts function into block editor hook
add_action( 'enqueue_block_editor_assets', 'ect_pro_gutenberg_scripts' );

/**
 * Block Initializer.
 */
add_action( 'plugins_loaded', function () {
	if ( function_exists( 'register_block_type' ) ) {
		// Hook server side rendering into render callback

		register_block_type(
			'ect/shortcode', array(
				'render_callback' => 'ect_pro_block_callback',
				'attributes'	  => array(
					'category'	 => array(
						'type' => 'string',
						'default' =>'all',
					),
					'template'	 => array(
						'type' => 'string',
						'default' =>'default',
					),
					'style'	 => array(
						'type' => 'string',
						'default' =>'style-1',
					),
					'order'	 => array(
						'type' => 'string',
						'default' =>'ASC',
					),
					'time'	 => array(
						'type' => 'string',
						'default' =>'future',
					),
					'dateformat'	=> array(
						'type'	=> 'string',
						'default' => 'default',
					),
					'limit'	=> array(
						'type'	=> 'string',
						'default' => '10',
					),
					'startDate'	=> array(
						'type'	=> 'string',
						'default' => '',
					),
					'endDate'	=> array(
						'type'	=> 'string',
						'default' => '',
					),
					'featuredonly'	=> array(
						'type'	=> 'string',
						'default' => 'false',
					),
					'columns'	=> array(
						'type'	=> 'string',
						'default' => 2,
					),
					'autoplay'	=> array(
						'type'	=> 'string',
						'default' =>'true',
					),
					'hideVenue'	=> array(
						'type'	=> 'string',
						'default' =>'no',
					),
					'tags'	=> array(
						'type'	=> 'string',
						'default' =>'',
					),
					'venues'	=> array(
						'type'	=> 'string',
						'default' =>'',
					),
					'organizers'	=> array(
						'type'	=> 'string',
						'default' =>'',
					),
					'socialshare'=> array(
						'type'	=> 'string',
						'default' =>'no',
					)
				),
			)
		);
	}
} );

/**
 * Block Output.
 */
function ect_pro_block_callback( $attr ) {
	extract( $attr );
	
	if ( isset( $template ) ) {
		$shortcode_string = '[events-calendar-templates template="%s" 
		style="%s" category="%s" date_format="%s"
		start_date="%s" end_date="%s" limit="%s"
		order="%s" hide-venue="%s"
		time="%s"  columns="%s" 
		autoplay="%s" featured-only="%s"
		 tags="%s" venues="%s" organizers="%s" socialshare="%s"]';
		$shortcode=  sprintf( $shortcode_string, $template, $style, 
		$category,$dateformat,$startDate,$endDate,$limit,
		$order,$hideVenue,$time,$columns,$autoplay,$featuredonly,$tags,$venues,$organizers,$socialshare);
		return $shortcode;
	}
}
