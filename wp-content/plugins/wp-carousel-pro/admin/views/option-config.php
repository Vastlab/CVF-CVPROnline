<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.

//
// Set a unique slug-like ID
//
$prefix = 'sp_wpcp_settings';

//
// Create options
//
SP_WPCP::createOptions(
	$prefix,
	array(
		'menu_title'         => __( 'Settings', 'wp-carousel-pro' ),
		'menu_slug'          => 'wpcp_settings',
		'menu_parent'        => 'edit.php?post_type=sp_wp_carousel',
		'menu_type'          => 'submenu',
		'ajax_save'          => true,
		'save_defaults'      => true,
		'show_reset_all'     => true,
		'framework_title'    => __( 'WordPress Carousel Pro', 'wp-carousel-pro' ),
		'framework_class'    => 'sp-wpcp-options',
		'theme'              => 'light',
		// menu extras.
		'show_bar_menu'      => false,
		'show_sub_menu'      => false,
		'show_network_menu'  => false,
		'show_in_customizer' => false,
		'show_search'        => false,
		// 'show_reset_all'     => true,
		'show_reset_section' => true,
		'show_all_options'   => false,
	)
);

//
// Create a section
//
SP_WPCP::createSection(
	$prefix,
	array(
		'title'  => 'Advanced Settings',
		'icon'   => 'fa fa-cogs',
		'fields' => array(
			array(
				'id'       => 'wpcp_delete_all_data',
				'type'     => 'checkbox',
				'title'    => __( 'Remove Data when Delete', 'wp-carousel-pro' ),
				'subtitle' => __( 'Check to remove plugin\'s data when plugin is uninstalled or deleted.', 'wp-carousel-pro' ),
				'default'  => false,
			),
			array(
				'id'         => 'wpcp_dequeue_google_font',
				'type'       => 'switcher',
				'title'      => __( 'Google Fonts', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Enqueue/Dequeue google font.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Enqueue', 'wp-carousel-pro' ),
				'text_off'   => __( 'Dequeue', 'wp-carousel-pro' ),
				'text_width' => 95,
				'default'    => true,
			),
			array(
				'type'    => 'subheading',
				'content' => __( 'Responsive Mode', 'wp-carousel-pro' ),
			),
			array(
				'id'           => 'wpcp_responsive_screen_setting',
				'type'         => 'column',
				'title'        => __( 'Maximum Screen Width', 'wp-carousel-pro' ),
				'subtitle'     => __( 'Set screen sizes for responsive mdoe.', 'wp-carousel-pro' ),
				'min'          => '300',
				'unit'         => true,
				'units'        => array(
					'px',
				),
				'lg_desktop'   => false,
				'desktop_icon' => 'Desktop',
				'laptop_icon'  => 'Laptop',
				'tablet_icon'  => 'Tablet',
				'mobile_icon'  => 'Mobile',
				'default'      => array(
					'desktop' => '1200',
					'laptop'  => '980',
					'tablet'  => '736',
					'mobile'  => '480',
				),
			),
			array(
				'type'    => 'subheading',
				'content' => __( 'Enqueue or Dequeue CSS', 'wp-carousel-pro' ),
			),
			array(
				'id'         => 'wpcp_enqueue_slick_css',
				'type'       => 'switcher',
				'title'      => __( 'Slick CSS', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Enqueue/Dequeue slick CSS.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Enqueue', 'wp-carousel-pro' ),
				'text_off'   => __( 'Dequeue', 'wp-carousel-pro' ),
				'text_width' => 95,
				'default'    => true,
			),
			array(
				'id'         => 'wpcp_enqueue_bx_css',
				'type'       => 'switcher',
				'title'      => __( 'bxSlider CSS', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Enqueue/Dequeue bxslider CSS.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Enqueue', 'wp-carousel-pro' ),
				'text_off'   => __( 'Dequeue', 'wp-carousel-pro' ),
				'text_width' => 95,
				'default'    => true,
			),
			array(
				'id'         => 'wpcp_enqueue_fa_css',
				'type'       => 'switcher',
				'title'      => __( 'Font Awesome CSS', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Enqueue/Dequeue font awesome CSS.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Enqueue', 'wp-carousel-pro' ),
				'text_off'   => __( 'Dequeue', 'wp-carousel-pro' ),
				'text_width' => 95,
				'default'    => true,
			),
			array(
				'id'         => 'wpcp_enqueue_magnific_css',
				'type'       => 'switcher',
				'title'      => __( 'Magnific Popup CSS', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Enqueue/Dequeue magnific popup CSS.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Enqueue', 'wp-carousel-pro' ),
				'text_off'   => __( 'Dequeue', 'wp-carousel-pro' ),
				'text_width' => 95,
				'default'    => true,
			),
			array(
				'type'    => 'subheading',
				'content' => __( 'Enqueue or Dequeue JS', 'wp-carousel-pro' ),
			),
			array(
				'id'         => 'wpcp_slick_js',
				'type'       => 'switcher',
				'title'      => __( 'Slick JS', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Enqueue/Dequeue slick JS.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Enqueue', 'wp-carousel-pro' ),
				'text_off'   => __( 'Dequeue', 'wp-carousel-pro' ),
				'text_width' => 95,
				'default'    => true,
			),
			array(
				'id'         => 'wpcp_bx_js',
				'type'       => 'switcher',
				'title'      => __( 'bxSlider JS', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Enqueue/Dequeue bxSlider JS.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Enqueue', 'wp-carousel-pro' ),
				'text_off'   => __( 'Dequeue', 'wp-carousel-pro' ),
				'text_width' => 95,
				'default'    => true,
			),
			array(
				'id'         => 'wpcp_magnific_js',
				'type'       => 'switcher',
				'title'      => __( 'Magnific Popup JS', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Enqueue/Dequeue Magnific popup JS.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Enqueue', 'wp-carousel-pro' ),
				'text_off'   => __( 'Dequeue', 'wp-carousel-pro' ),
				'text_width' => 95,
				'default'    => true,
			),

		),
	)
);

//
// Custom CSS Fields
//
SP_WPCP::createSection(
	$prefix,
	array(
		'id'     => 'custom_css_section',
		'title'  => __( 'Custom CSS', 'wp-carousel-pro' ),
		'icon'   => 'fa fa-css3',
		'fields' => array(
			array(
				'id'       => 'wpcp_custom_css',
				'type'     => 'code_editor',
				'title'    => __( 'Custom CSS', 'wp-carousel-pro' ),
				'subtitle' => __( 'Write your custom css.', 'wp-carousel-pro' ),
				'settings' => array(
					'mode'  => 'css',
					'theme' => 'monokai',
				),
			),
		),
	)
);


