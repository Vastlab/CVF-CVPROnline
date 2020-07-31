<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access pages directly.

//
// Metabox of the uppers section / Upload section.
// Set a unique slug-like ID.
//
$wpcp_carousel_content_source_settings = 'sp_wpcp_upload_options';

//
// Create a metabox.
//
SP_WPCP::createMetabox(
	$wpcp_carousel_content_source_settings,
	array(
		'title'        => __( 'WordPress Carousel Pro', 'wp-carousel-pro' ),
		'post_type'    => 'sp_wp_carousel',
		'show_restore' => false,
		'context'      => 'normal',
	)
);

//
// Create a section.
//
SP_WPCP::createSection(
	$wpcp_carousel_content_source_settings,
	array(
		'fields' => array(
			array(
				'type'  => 'heading',
				'image' => plugin_dir_url( __DIR__ ) . 'img/wpcp-logo.png',
				'after' => '<i class="fa fa-life-ring"></i> Support',
				'link'  => 'https://shapedplugin.com/support/',
				'class' => 'wpcp-admin-header',
			),
			array(
				'id'      => 'wpcp_carousel_type',
				'type'    => 'carousel_type',
				'title'   => __( 'Carousel Type', 'wp-carousel-pro' ),
				'options' => array(
					'image-carousel'   => array(
						'icon' => 'fa fa-image',
						'text' => __( 'Image', 'wp-carousel-pro' ),
					),
					'post-carousel'    => array(
						'icon' => 'dashicons dashicons-admin-post',
						'text' => __( 'Post', 'wp-carousel-pro' ),
					),
					'product-carousel' => array(
						'icon' => 'fa fa-cart-plus',
						'text' => __( 'Woo Product', 'wp-carousel-pro' ),
					),
					'content-carousel' => array(
						'icon' => 'fa fa-file-text-o',
						'text' => __( 'Content', 'wp-carousel-pro' ),
					),
					'video-carousel'   => array(
						'icon' => 'fa fa-play-circle-o',
						'text' => __( 'Video', 'wp-carousel-pro' ),
					),
				),
				'default' => 'image-carousel',
			),
			array(
				'id'          => 'wpcp_gallery',
				'type'        => 'gallery',
				'title'       => 'Gallery Images',
				'wrap_class'  => 'wpcp-gallery-filed-wrapper',
				'add_title'   => __( 'ADD IMAGE', 'wp-carousel-pro' ),
				'edit_title'  => __( 'EDIT IMAGE', 'wp-carousel-pro' ),
				'clear_title' => __( 'REMOVE ALL', 'wp-carousel-pro' ),
				'dependency'  => array( 'wpcp_carousel_type', '==', 'image-carousel' ),
			),
			// Post Carousel.
			array(
				'id'         => 'wpcp_post_type',
				'type'       => 'select',
				'title'      => __( 'Post Type', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Select a post type to display the posts from.', 'wp-carousel-pro' ),
				'options'    => 'post_type',
				'class'      => 'sp_wpcp_post_type',
				'attributes' => array(
					'placeholder' => __( 'Select Post Type', 'wp-carousel-pro' ),
					'style'       => 'min-width: 150px;',
				),
				'default'    => 'post',
				'dependency' => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
			),
			array(
				'id'         => 'wpcp_display_posts_from',
				'type'       => 'select',
				'title'      => __( 'Filter Posts', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Select an option to filter the posts.', 'wp-carousel-pro' ),
				'options'    => array(
					'latest'        => __( 'Latest', 'wp-carousel-pro' ),
					'taxonomy'      => __( 'Taxonomy', 'wp-carousel-pro' ),
					'specific_post' => __( 'Specific', 'wp-carousel-pro' ),
				),
				'default'    => 'latest',
				'class'      => 'chosen',
				'dependency' => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
			),

			array(
				'id'         => 'wpcp_post_taxonomy',
				'type'       => 'select',
				'title'      => __( 'Select Taxonomy', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Choose the taxonomy.', 'wp-carousel-pro' ),
				'options'    => 'taxonomies',
				'class'      => 'sp_wpcp_post_taxonomy',
				'dependency' => array( 'wpcp_display_posts_from|wpcp_carousel_type', '==|==', 'taxonomy|post-carousel' ),
			),
			array(
				'id'          => 'wpcp_taxonomy_terms',
				'type'        => 'select',
				'title'       => __( 'Choose Term(s)', 'wp-carousel-pro' ),
				'subtitle'    => __( 'Choose the taxonomy term(s) to show the posts from.', 'wp-carousel-pro' ),
				'options'     => 'terms',
				'class'       => 'sp_wpcp_taxonomy_terms',
				'attributes'  => array(
					'style' => 'width: 280px;',
				),
				'multiple'    => true,
				'placeholder' => __( 'Select Term(s)', 'wp-carousel-pro' ),
				'chosen'      => true,
				'dependency'  => array( 'wpcp_display_posts_from|wpcp_carousel_type', '==|==', 'taxonomy|post-carousel' ),

			),
			array(
				'id'         => 'taxonomy_operator',
				'type'       => 'select',
				'title'      => __( 'Operator', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Choose the taxonomy term(s) operator.', 'wp-carousel-pro' ),
				'options'    => array(
					'IN'     => __( 'IN - Show posts which associate with one or more terms', 'wp-carousel-pro' ),
					'AND'    => __( 'AND - Show posts which match all terms', 'wp-carousel-pro' ),
					'NOT IN' => __( 'NOT IN - Show posts which don\'t match the terms', 'wp-carousel-pro' ),
				),
				'default'    => 'IN',
				'dependency' => array( 'wpcp_display_posts_from|wpcp_carousel_type', '==|==', 'taxonomy|post-carousel' ),
			),
			array(
				'id'         => 'number_of_total_posts',
				'type'       => 'spinner',
				'title'      => __( 'Total Posts', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Number of total posts to show. Default value is 10.', 'wp-carousel-pro' ),
				'default'    => '10',
				'min'        => 1,
				'max'        => 1000,
				'dependency' => array( 'wpcp_display_posts_from|wpcp_carousel_type', '!=|==', 'specific_post|post-carousel' ),
			),
			array(
				'id'          => 'wpcp_specific_posts',
				'type'        => 'select',
				'title'       => __( 'Select Posts', 'wp-carousel-pro' ),
				'subtitle'    => __( 'Choose the posts to display.', 'wp-carousel-pro' ),
				'options'     => 'all_posts',
				'chosen'      => true,
				'class'       => 'sp_wpcp_specific_posts',
				'multiple'    => true,
				'placeholder' => __( 'Choose Posts', 'wp-carousel-pro' ),
				'query_args'  => array(
					'posts_per_page' => -1,
					'cache_results'  => false,
					'no_found_rows'  => true,
				),
				'dependency'  => array(
					'wpcp_display_posts_from|wpcp_carousel_type',
					'==|==',
					'specific_post|post-carousel',
				),
			),
			// Product Carousel.
			array(
				'id'         => 'wpcp_display_product_from',
				'type'       => 'select',
				'title'      => __( 'Filter Products', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Select an option to display the products.', 'wp-carousel-pro' ),
				'options'    => array(
					'latest'            => __( 'Latest', 'wp-carousel-pro' ),
					'taxonomy'          => __( 'Category', 'wp-carousel-pro' ),
					'specific_products' => __( 'Specific', 'wp-carousel-pro' ),
				),
				'default'    => 'latest',
				'class'      => 'chosen',
				'dependency' => array( 'wpcp_carousel_type', '==', 'product-carousel' ),
			),
			array(
				'id'          => 'wpcp_specific_product',
				'type'        => 'select',
				'title'       => __( 'Select Product', 'wp-carousel-pro' ),
				'subtitle'    => __( 'Choose the products to display. Search option is available.', 'wp-carousel-pro' ),
				'options'     => 'posts',
				'query_args'  => array(
					'post_type'     => 'product',
					'orderby'       => 'post_date',
					'order'         => 'DESC',
					'numberposts'   => 30000,
					'cache_results' => false,
					'no_found_rows' => true,
				),
				'chosen'      => true,
				'multiple'    => true,
				'placeholder' => __( 'Choose Product', 'wp-carousel-pro' ),
				'dependency'  => array( 'wpcp_display_product_from|wpcp_carousel_type', '==|==', 'specific_products|product-carousel' ),
			),
			array(
				'id'          => 'wpcp_taxonomy_terms',
				'type'        => 'select',
				'title'       => __( 'Category Term(s)', 'wp-carousel-pro' ),
				'subtitle'    => __( 'Choose the product category term(s).', 'wp-carousel-pro' ),
				'options'     => 'categories',
				'query_args'  => array(
					'post_type' => 'product',
					'taxonomy'  => 'product_cat',
				),
				'chosen'      => true,
				'multiple'    => true,
				'placeholder' => __( 'Choose term(s)', 'wp-carousel-pro' ),
				'dependency'  => array( 'wpcp_display_product_from|wpcp_carousel_type', '==|==', 'taxonomy|product-carousel' ),
				'attributes'  => array(

					'style' => 'min-width: 250px;',
				),
			),
			array(
				'id'         => 'wpcp_product_category_operator',
				'type'       => 'select',
				'title'      => __( 'Operator', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Select the category term operator.', 'wp-carousel-pro' ),
				'options'    => array(
					'IN'     => __( 'IN - Show products which associate with one or more terms', 'wp-carousel-pro' ),
					'AND'    => __( 'AND - Show products which match all terms', 'wp-carousel-pro' ),
					'NOT IN' => __( 'NOT IN - Show products which don\'t match the terms', 'wp-carousel-pro' ),
				),
				'default'    => 'IN',
				'class'      => 'chosen',
				'dependency' => array( 'wpcp_display_product_from|wpcp_carousel_type', '==|==', 'taxonomy|product-carousel' ),
			),
			array(
				'id'         => 'wpcp_total_products',
				'type'       => 'spinner',
				'title'      => __( 'Total Products', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Number of total products to display. Default value is 10.', 'wp-carousel-pro' ),
				'default'    => '10',
				'min'        => 1,
				'max'        => 1000,
				'dependency' => array( 'wpcp_display_product_from|wpcp_carousel_type', '!=|==', 'specific_products|product-carousel' ),
			),
			// Content Carousel.
			array(
				'id'                     => 'carousel_content_source',
				'type'                   => 'group',
				'title'                  => __( 'Content', 'wp-carousel-pro' ),
				'button_title'           => __( 'Add Content', 'wp-carousel-pro' ),
				'class'                  => 'wpcp_carousel_content_wrapper',
				'accordion_title_prefix' => __( 'Slide Content', 'wp-carousel-pro' ),
				'accordion_title_number' => true,
				'accordion_title_auto'   => false,
				'fields'                 => array(
					array(
						'id'     => 'carousel_content_description',
						'type'   => 'wp_editor',
						'class'  => 'wpcp_carousel_content_source',
						'title'  => __( 'Slide Content', 'wp-carousel-pro' ),
						'height' => '150px',
					),
					array(
						'id'                       => 'wpcp_carousel_content_bg',
						'type'                     => 'background_adv',
						'class'                    => 'wpcp_carousel_content_bg',
						'title'                    => __( 'Slide Background & Settings', 'wp-carousel-pro' ),
						'background_gradient'      => true,
						'default'                  => array(
							'background-color'          => '#cfe2f3',
							'background-gradient-color' => '#555',
							'background-size'           => 'initial',
							'background-position'       => 'center center',
							'background-repeat'         => 'no-repeat',
							'background-attachment'     => 'scroll',

						),
						'background_image_preview' => false,
						'preview'                  => 'always',
					),
				),
				'dependency'             => array( 'wpcp_carousel_type', '==', 'content-carousel' ),
			), // End of Content Carousel.
			// Video.
			array(
				'id'                     => 'carousel_video_source',
				'type'                   => 'group',
				'class'                  => 'wpcp-video-field-wrapper',
				'title'                  => __( 'Videos', 'wp-carousel-pro' ),
				'button_title'           => __( 'Add Video', 'wp-carousel-pro' ),
				'accordion_title_prefix' => __( 'Video', 'wp-carousel-pro' ),
				'accordion_title_number' => true,
				'fields'                 => array(
					array(
						'id'      => 'carousel_video_source_type',
						'type'    => 'select',
						'title'   => 'Source',
						'options' => array(
							'youtube'     => __( 'YouTube.', 'wp-carousel-pro' ),
							'vimeo'       => __( 'Vimeo.', 'wp-carousel-pro' ),
							'dailymotion' => __( 'Dailymotion.', 'wp-carousel-pro' ),
							'self_hosted' => __( 'Self-Hosted.', 'wp-carousel-pro' ),
							'image_only'  => __( 'Image Only', 'wp-carousel-pro' ),
						),
						'default' => 'youtube',
					),
					array(
						'id'         => 'carousel_video_source_id',
						'type'       => 'text',
						'title'      => __( 'Video ID', 'wp-carousel-pro' ),
						'subtitle'   => __( 'The last part of the URL is the ID e.g //vimeo.com/<code>95746815</code><br>//youtube.com/watch?v=<code>eKFTSSKCzWA</code>', 'wp-carousel-pro' ),
						'dependency' => array( 'carousel_video_source_type', 'any', 'youtube,vimeo,dailymotion' ),
					),
					array(
						'id'           => 'carousel_video_source_thumb',
						'type'         => 'media',
						'library'      => array( 'image' ),
						'url'          => false,
						'preview'      => true,
						'title'        => __( 'Thumbnail Image', 'wp-carousel-pro' ),
						'button_title' => __( 'Upload Image', 'wp-carousel-pro' ),
						'remove_title' => __( 'Remove', 'wp-carousel-pro' ),
						'placeholder'  => __( 'No image selected', 'wp-carousel-pro' ),
						'dependency'   => array( 'carousel_video_source_type', 'any', 'self_hosted,image_only' ),
					),
					array(
						'id'           => 'carousel_video_source_upload',
						'type'         => 'upload',
						'title'        => __( 'Upload Video', 'wp-carousel-pro' ),
						'upload_type'  => 'video',
						'button_title' => __( 'Upload Video', 'wp-carousel-pro' ),
						'remove_title' => __( 'Remove', 'wp-carousel-pro' ),
						'placeholder'  => __( 'No video selected', 'wp-carousel-pro' ),
						'library'      => array( 'video' ),
						'dependency'   => array( 'carousel_video_source_type', '==', 'self_hosted' ),
					),
					array(
						'id'         => 'carousel_image_only_link_action',
						'type'       => 'button_set',
						'title'      => __( 'Click Action', 'wp-carousel-pro' ),
						'subtitle'   => __( 'Set action for click on this image.', 'wp-carousel-pro' ),
						'options'    => array(
							'img_link' => __( 'Open Link', 'wp-carousel-pro' ),
							'img_lbox' => __( 'Lightbox', 'wp-carousel-pro' ),
						),
						'attributes' => array(
							'data-depend-id' => 'carousel_image_only_link_action',
						),
						'radio'      => true,
						'default'    => 'img_link',
						'dependency' => array( 'carousel_video_source_type', '==', 'image_only' ),
					),

					array(
						'id'     => 'carousel_video_description',
						'class'  => 'wpcp-video-description',
						'type'   => 'wp_editor',
						'title'  => __( 'Title & Description', 'wp-carousel-pro' ),
						'height' => '150px',
					),
				),
				'dependency'             => array( 'wpcp_carousel_type', '==', 'video-carousel' ),
			), // End of Video Carousel.
		), // End of fields array.
	)
);

//
// Metabox for the Carousel Post Type.
// Set a unique slug-like ID.
//
$wpcp_carousel_shortcode_settings = 'sp_wpcp_shortcode_options';

//
// Create a metabox.
//
SP_WPCP::createMetabox(
	$wpcp_carousel_shortcode_settings,
	array(
		'title'        => __( 'Shortcode Section', 'wp-carousel-pro' ),
		'post_type'    => 'sp_wp_carousel',
		'show_restore' => false,
		'theme'        => 'light',
		'class'        => 'sp_wpcp_shortcode_generator',
	)
);

//
// Create a section.
//
SP_WPCP::createSection(
	$wpcp_carousel_shortcode_settings,
	array(
		'title'  => __( 'General Settings', 'wp-carousel-pro' ),
		'icon'   => 'fa fa-cog',
		'fields' => array(
			array(
				'id'         => 'section_title',
				'type'       => 'switcher',
				'title'      => __( 'Carousel Section Title', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide the carousel section title.', 'wp-carousel-pro' ),
				'default'    => false,
				'text_on'    => __( 'Show', 'wp-carousel-pro' ),
				'text_off'   => __( 'Hide', 'wp-carousel-pro' ),
				'text_width' => 75,
			),
			array(
				'id'              => 'section_title_margin_bottom',
				'type'            => 'spacing',
				'title'           => __( 'Carousel Title Margin Bottom', 'wp-carousel-pro' ),
				'subtitle'        => __( 'Set margin bottom for the carousel section title. Default value is 30px.', 'wp-carousel-pro' ),
				'all'             => true,
				'all_text'        => '<i class="fa fa-long-arrow-down"></i>',
				'units'           => array(
					'px',
				),
				'all_placeholder' => 'margin',
				'default'         => array(
					'all' => '30',
				),
				'dependency'      => array(
					'section_title',
					'==',
					'true',
					true,
				),
			),
			array(
				'id'       => 'wpcp_carousel_mode',
				'type'     => 'button_set',
				'title'    => __( 'Carousel Mode', 'wp-carousel-pro' ),
				'subtitle' => __( 'Set carousel mode. Carousel controls are disabled in the ticker mode.', 'wp-carousel-pro' ),
				'options'  => array(
					'standard' => __( 'Standard', 'wp-carousel-pro' ),
					'ticker'   => __( 'Ticker', 'wp-carousel-pro' ),
					'center'   => __( 'Center', 'wp-carousel-pro' ),
				),
				'default'  => 'standard',
			),
			array(
				'id'              => 'wpcp_slide_width',
				'type'            => 'spacing',
				'title'           => __( 'Slide Width', 'wp-carousel-pro' ),
				'subtitle'        => __( 'Set a width for the each slide.', 'wp-carousel-pro' ),
				'all'             => true,
				'all_text'        => '<i class="fa fa-arrows-h"></i>',
				'all_placeholder' => 'width',
				'default'         => array(
					'all' => '250',
				),
				'units'           => array(
					'px',
				),
				'attributes'      => array(
					'min' => 0,
				),
				'dependency'      => array( 'wpcp_carousel_mode', '==', 'ticker' ),
			),
			array(
				'id'         => 'wpcp_number_of_columns',
				'type'       => 'column',
				'title'      => __( 'Carousel Column(s)', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set number of column on devices.', 'wp-carousel-pro' ),
				'default'    => array(
					'lg_desktop' => '5',
					'desktop'    => '4',
					'laptop'     => '3',
					'tablet'     => '2',
					'mobile'     => '1',
				),
				'min'        => '0',
				'dependency' => array( 'wpcp_carousel_mode', '!=', 'ticker' ),
			),
			array(
				'id'         => 'wpcp_image_center_mode_padding',
				'type'       => 'column',
				'title'      => __( 'Center Padding', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set center mode padding.', 'wp-carousel-pro' ),
				'unit'       => true,
				'units'      => array(
					'px',
				),
				'default'    => array(
					'lg_desktop' => '100',
					'desktop'    => '100',
					'laptop'     => '70',
					'tablet'     => '50',
					'mobile'     => '40',
				),
				'dependency' => array( 'wpcp_carousel_mode', '==', 'center' ),
			),
			array(
				'id'         => 'wpcp_number_of_columns_ticker',
				'type'       => 'column',
				'title'      => __( 'Carousel Column(s)', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set maximum & minimum column number.', 'wp-carousel-pro' ),
				'desktop'    => false,
				'laptop'     => false,
				'tablet'     => false,
				'default'    => array(
					'lg_desktop' => '5',
					'mobile'     => '2',
				),
				'dependency' => array( 'wpcp_carousel_mode', '==', 'ticker' ),
			),
			array(
				'id'         => 'wpcp_logo_link_show',
				'type'       => 'switcher',
				'title'      => __( 'Image Link', 'wp-carousel-pro' ),
				'subtitle'   => __( 'On/Off to enable and disable the image link.', 'wp-carousel-pro' ),
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type', '==', 'image-carousel', true ),
			),
			array(
				'id'         => 'wpcp_logo_link_nofollow',
				'type'       => 'checkbox',
				'title'      => __( 'Nofollow', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Check to add nofollow to the image link.', 'wp-carousel-pro' ),
				'default'    => false,
				'dependency' => array( 'wpcp_carousel_type|wpcp_logo_link_show', '==|==', 'image-carousel|true', true ),
			),
			array(
				'id'         => 'wpcp_link_open_target',
				'type'       => 'select',
				'title'      => __( 'Link Target', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set a target for the image link.', 'wp-carousel-pro' ),
				'options'    => array(
					'_self'   => __( 'Self', 'wp-carousel-pro' ),
					'_blank'  => __( 'New', 'wp-carousel-pro' ),
					'_parent' => __( 'Parent', 'wp-carousel-pro' ),
					'_top'    => __( 'Top', 'wp-carousel-pro' ),
				),
				'default'    => '_blank',
				'dependency' => array( 'wpcp_carousel_type', 'any', 'image-carousel,post-carousel', true ),
			),
			array(
				'id'         => 'wpcp_image_order_by',
				'type'       => 'select',
				'title'      => __( 'Order by', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set an order by option.', 'wp-carousel-pro' ),
				'options'    => array(
					'menu_order' => __( 'Drag & Drop', 'wp-carousel-pro' ),
					'rand'       => __( 'Random', 'wp-carousel-pro' ),
				),
				'default'    => 'menu_order',
				'dependency' => array( 'wpcp_carousel_type', 'any', 'image-carousel,video-carousel,content-carousel', true ),
			),
			array(
				'id'         => 'wpcp_post_order_by',
				'type'       => 'select',
				'title'      => __( 'Order by', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Select an order by option.', 'wp-carousel-pro' ),
				'options'    => array(
					'ID'         => __( 'ID', 'wp-carousel-pro' ),
					'date'       => __( 'Date', 'wp-carousel-pro' ),
					'rand'       => __( 'Random', 'wp-carousel-pro' ),
					'title'      => __( 'Title', 'wp-carousel-pro' ),
					'modified'   => __( 'Modified', 'wp-carousel-pro' ),
					'menu_order' => __( 'Menu Order', 'wp-carousel-pro' ),
				),
				'default'    => 'menu_order',
				'dependency' => array( 'wpcp_carousel_type', 'any', 'post-carousel,product-carousel', true ),
			),
			array(
				'id'         => 'wpcp_post_order',
				'type'       => 'select',
				'title'      => __( 'Order', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Select an order option.', 'wp-carousel-pro' ),
				'options'    => array(
					'ASC'  => __( 'Ascending', 'wp-carousel-pro' ),
					'DESC' => __( 'Descending', 'wp-carousel-pro' ),
				),
				'default'    => 'rand',
				'dependency' => array( 'wpcp_carousel_type', 'any', 'post-carousel,product-carousel', true ),
			),
			array(
				'id'       => 'wpcp_preloader',
				'type'     => 'switcher',
				'title'    => __( 'Preloader', 'wp-carousel-pro' ),
				'subtitle' => __( 'Carousel will be hidden until page load completed.', 'wp-carousel-pro' ),
				'default'  => true,
			),
		), // Fields array end.
	)
); // End of Upload section.

//
// Carousel settings section begin.
//
SP_WPCP::createSection(
	$wpcp_carousel_shortcode_settings,
	array(
		'title'  => __( 'Carousel Settings', 'wp-carousel-pro' ),
		'icon'   => 'fa fa-sliders',
		'fields' => array(
			array(
				'id'         => 'wpcp_carousel_auto_play',
				'type'       => 'switcher',
				'title'      => __( 'AutoPlay', 'wp-carousel-pro' ),
				'subtitle'   => __( 'On/Off auto play.', 'wp-carousel-pro' ),
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_mode', '!=', 'ticker', true ),
			),
			array(
				'id'              => 'carousel_auto_play_speed',
				'type'            => 'spacing',
				'title'           => __( 'AutoPlay Speed', 'wp-carousel-pro' ),
				'subtitle'        => __( 'Set auto play speed. Default value is 3000 milliseconds.', 'wp-carousel-pro' ),
				'all'             => true,
				'all_text'        => false,
				'all_placeholder' => 'speed',
				'default'         => array(
					'all' => '3000',
				),
				'units'           => array(
					'ms',
				),
				'attributes'      => array(
					'min' => 0,
				),
				'dependency'      => array(
					'wpcp_carousel_auto_play|wpcp_carousel_mode',
					'==|!=',
					'true|ticker',
				),
			),
			array(
				'id'              => 'standard_carousel_scroll_speed',
				'type'            => 'spacing',
				'title'           => __( 'Sliding Speed', 'wp-carousel-pro' ),
				'subtitle'        => __( 'Set sliding or scrolling speed. Default value is 600 milliseconds.', 'wp-carousel-pro' ),
				'all'             => true,
				'all_text'        => false,
				'all_placeholder' => 'speed',
				'default'         => array(
					'all' => '600',
				),
				'units'           => array(
					'ms',
				),
				'attributes'      => array(
					'min' => 0,
				),
				'dependency'      => array( 'wpcp_carousel_mode', '!=', 'ticker' ),
			),
			array(
				'id'              => 'ticker_carousel_scroll_speed',
				'type'            => 'spacing',
				'title'           => __( 'Sliding Speed', 'wp-carousel-pro' ),
				'subtitle'        => __( 'Set sliding or scrolling speed. Default value is 8000 milliseconds.', 'wp-carousel-pro' ),
				'all'             => true,
				'all_text'        => false,
				'all_placeholder' => 'speed',
				'default'         => array(
					'all' => '8000',
				),
				'units'           => array(
					'ms',
				),
				'attributes'      => array(
					'min' => 0,
				),
				'dependency'      => array( 'wpcp_carousel_mode', '==', 'ticker' ),
			),
			array(
				'id'         => 'slides_to_scroll',
				'type'       => 'column',
				'title'      => __( 'Slide to Scroll', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Number of slide(s) to scroll at a time.', 'wp-carousel-pro' ),
				'unit'       => false,
				'default'    => array(
					'lg_desktop' => '1',
					'desktop'    => '1',
					'laptop'     => '1',
					'tablet'     => '1',
					'mobile'     => '1',
				),
				'dependency' => array( 'wpcp_carousel_mode', '!=', 'ticker' ),
			),
			array(
				'id'       => 'carousel_pause_on_hover',
				'type'     => 'switcher',
				'title'    => __( 'Pause on Hover', 'wp-carousel-pro' ),
				'subtitle' => __( 'On/Off carousel pause on hover.', 'wp-carousel-pro' ),
				'default'  => true,
			),
			array(
				'id'       => 'carousel_infinite',
				'type'     => 'switcher',
				'title'    => __( 'Infinite Loop', 'wp-carousel-pro' ),
				'subtitle' => __( 'On/Off infinite loop mode.', 'wp-carousel-pro' ),
				'default'  => true,
				'dependency' => array( 'wpcp_carousel_mode', '!=', 'ticker' ),
			),
			array(
				'id'       => 'wpcp_carousel_direction',
				'type'     => 'button_set',
				'title'    => __( 'Carousel Direction', 'wp-carousel-pro' ),
				'subtitle' => __( 'Set carousel direction as you need.', 'wp-carousel-pro' ),
				'options'  => array(
					'rtl' => __( 'Right to Left', 'wp-carousel-pro' ),
					'ltr' => __( 'Left to Right', 'wp-carousel-pro' ),
				),
				'radio'    => true,
				'default'  => 'rtl',
			),

			array(
				'id'         => 'wpcp_slider_animation',
				'type'       => 'radio',
				'title'      => __( 'Slide Effect', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set a sliding effect.', 'wp-carousel-pro' ),
				'options'    => array(
					'slide' => __( 'Slide', 'wp-carousel-pro' ),
					'fade'  => __( 'Fade (For single column view.)', 'wp-carousel-pro' ),
				),
				'default'    => 'slide',
				'attributes' => array(
					'data-depend-id' => 'slider_animation',
				),
				'dependency' => array( 'wpcp_carousel_mode', '!=', 'ticker' ),
			),
			// Navigation.
			array(
				'type'       => 'subheading',
				'content'    => __( 'Navigation', 'wp-carousel-pro' ),
				'dependency' => array( 'wpcp_carousel_mode', '!=', 'ticker' ),
			),
			array(
				'id'         => 'wpcp_navigation',
				'type'       => 'button_set',
				'title'      => __( 'Navigation', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide carousel navigation.', 'wp-carousel-pro' ),
				'options'    => array(
					'show'        => __( 'Show', 'wp-carousel-pro' ),
					'hide'        => __( 'Hide', 'wp-carousel-pro' ),
					'hide_mobile' => __( 'Hide on Mobile', 'wp-carousel-pro' ),
				),
				'radio'      => true,
				'default'    => 'hide_mobile',
				'attributes' => array(
					'data-depend-id' => 'wpcp_navigation',
				),
				'dependency' => array( 'wpcp_carousel_mode', '!=', 'ticker' ),
			),
			array(
				'id'         => 'wpcp_carousel_nav_position',
				'type'       => 'select',
				'title'      => __( 'Select Position', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Select a position for the navigation arrows.', 'wp-carousel-pro' ),
				'options'    => array(
					'top_right'                   => __( 'Top right', 'wp-carousel-pro' ),
					'top_center'                  => __( 'Top center', 'wp-carousel-pro' ),
					'top_left'                    => __( 'Top left', 'wp-carousel-pro' ),
					'bottom_left'                 => __( 'Bottom left', 'wp-carousel-pro' ),
					'bottom_center'               => __( 'Bottom center', 'wp-carousel-pro' ),
					'bottom_right'                => __( 'Bottom right', 'wp-carousel-pro' ),
					'vertical_center'             => __( 'Vertically center', 'wp-carousel-pro' ),
					'vertical_center_inner'       => __( 'Vertically center inner', 'wp-carousel-pro' ),
					'vertical_center_inner_hover' => __( 'Vertically center inner on hover', 'wp-carousel-pro' ),
				),
				'default'    => 'vertical_center',
				'class'      => 'chosen',
				'dependency' => array( 'wpcp_navigation|wpcp_carousel_mode', '!=|!=', 'hide|ticker' ),
			),
			array(
				'id'         => 'navigation_icons',
				'type'       => 'button_set',
				'title'      => __( 'Choose an Icon', 'wp-carousel-pro' ),
				'subtitle'   => __( 'choose a carousel navigation icon.', 'wp-carousel-pro' ),
				'class'      => 'wpcp_navigation_icons',
				'options'    => array(
					'angle'        => '<i class="fa fa-angle-right"></i>',
					'chevron'      => '<i class="fa fa-chevron-right"></i>',
					'angle-double' => '<i class="fa fa-angle-double-right"></i>',
					'arrow'        => '<i class="fa fa-arrow-right"></i>',
					'long-arrow'   => '<i class="fa fa-long-arrow-right"></i>',
					'caret'        => '<i class="fa fa-caret-right"></i>',
				),
				'default'    => 'angle',
				'radio'      => true,
				'dependency' => array(
					'wpcp_navigation|wpcp_carousel_mode',
					'!=|!=',
					'hide|ticker',
				),
			),
			array(
				'id'         => 'wpcp_hide_nav_bg_border',
				'type'       => 'checkbox',
				'title'      => __( 'Hide Border and Background', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Hide nav arrow border and background.', 'wp-carousel-pro' ),
				'default'    => false,
				'dependency' => array(
					'wpcp_navigation|wpcp_carousel_mode',
					'!=|!=',
					'hide|ticker',
				),
			),
			array(
				'id'         => 'navigation_icons_border_radius',
				'type'       => 'dimensions_advanced',
				'title'      => __( 'Border Radius', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set border radius for the navigation icons. Default value is 0.', 'wp-carousel-pro' ),
				'style'      => false,
				'color'      => false,
				'all'        => true,
				'unit'       => '%',
				'default'    => array(
					'all' => '0',
				),
				'attributes' => array(
					'min' => 0,
				),
				'dependency' => array(
					'wpcp_navigation|wpcp_carousel_mode|wpcp_hide_nav_bg_border',
					'!=|!=|==',
					'hide|ticker|false',
				),
			),
			array(
				'id'         => 'wpcp_nav_colors',
				'type'       => 'color_group',
				'title'      => __( 'Navigation Color', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set color for the carousel navigation.', 'wp-carousel-pro' ),
				'options'    => array(
					'color-1' => __( 'Color', 'wp-carousel-pro' ),
					'color-2' => __( 'Hover Color', 'wp-carousel-pro' ),
					'color-3' => __( 'Background', 'wp-carousel-pro' ),
					'color-4' => __( 'Hover Background', 'wp-carousel-pro' ),
					'color-5' => __( 'Border', 'wp-carousel-pro' ),
					'color-6' => __( 'Hover Border', 'wp-carousel-pro' ),
				),
				'default'    => array(
					'color-1' => '#aaa',
					'color-2' => '#fff',
					'color-3' => '#fff',
					'color-4' => '#18AFB9',
					'color-5' => '#aaa',
					'color-6' => '#18AFB9',
				),
				'dependency' => array(
					'wpcp_navigation|wpcp_carousel_mode',
					'!=|!=',
					'hide|ticker',
				),
			),
			// Pagination.
			array(
				'type'       => 'subheading',
				'content'    => __( 'Pagination', 'wp-carousel-pro' ),
				'dependency' => array( 'wpcp_carousel_mode', '!=', 'ticker' ),
			),
			array(
				'id'         => 'wpcp_pagination',
				'type'       => 'button_set',
				'title'      => __( 'Pagination', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide carousel pagination.', 'wp-carousel-pro' ),
				'options'    => array(
					'show'        => __( 'Show', 'wp-carousel-pro' ),
					'hide'        => __( 'Hide', 'wp-carousel-pro' ),
					'hide_mobile' => __( 'Hide on Mobile', 'wp-carousel-pro' ),
				),
				'radio'      => true,
				'default'    => 'show',
				'attributes' => array(
					'data-depend-id' => 'wpcp_pagination',
				),
				'dependency' => array( 'wpcp_carousel_mode', '!=', 'ticker' ),
			),
			array(
				'id'          => 'wpcp_pagination_margin',
				'type'        => 'spacing',
				'title'       => __( 'Pagination Margin', 'wp-carousel-pro' ),
				'subtitle'    => __( 'Set margin for carousel pagination.', 'wp-carousel-pro' ),
				'output_mode' => 'margin',
				'min'         => '-200',
				'default'     => array(
					'top'    => '18',
					'right'  => '0',
					'bottom' => '0',
					'left'   => '0',
					'unit'   => 'px',
				),
				'dependency'  => array( 'wpcp_pagination|wpcp_carousel_mode', '!=|!=', 'hide|ticker' ),
			),
			array(
				'id'         => 'wpcp_pagination_color',
				'type'       => 'color_group',
				'title'      => __( 'Pagination Color', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set color for the carousel pagination dots.', 'wp-carousel-pro' ),
				'options'    => array(
					'color1' => __( 'Color', 'wp-carousel-pro' ),
					'color2' => __( 'Active Color', 'wp-carousel-pro' ),
				),
				'default'    => array(
					'color1' => '#cccccc',
					'color2' => '#52b3d9',
				),
				'dependency' => array( 'wpcp_pagination|wpcp_carousel_mode', '!=|!=', 'hide|ticker' ),
			),

			// Miscellaneous.
			array(
				'type'       => 'subheading',
				'content'    => __( 'Miscellaneous', 'wp-carousel-pro' ),
				'dependency' => array( 'wpcp_carousel_mode', '!=', 'ticker' ),
			),

			array(
				'id'         => 'wpcp_adaptive_height',
				'type'       => 'switcher',
				'title'      => __( 'Adaptive Height', 'wp-carousel-pro' ),
				'subtitle'   => __( 'On/Off adaptive height for the carousel.', 'wp-carousel-pro' ),
				'default'    => false,
				'dependency' => array( 'wpcp_carousel_mode', '!=', 'ticker' ),
			),
			array(
				'id'         => 'slider_swipe',
				'type'       => 'switcher',
				'title'      => __( 'Touch Swipe', 'wp-carousel-pro' ),
				'subtitle'   => __( 'On/Off touch swipe mode.', 'wp-carousel-pro' ),
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_mode', '!=', 'ticker' ),
			),
			array(
				'id'         => 'slider_draggable',
				'type'       => 'switcher',
				'title'      => __( 'Mouse Draggable', 'wp-carousel-pro' ),
				'subtitle'   => __( 'On/Off mouse draggable mode.', 'wp-carousel-pro' ),
				'default'    => true,
				'dependency' => array(
					'slider_swipe|wpcp_carousel_mode',
					'==|!=',
					'true|ticker',
				),
			),
			array(
				'id'         => 'carousel_swipetoslide',
				'type'       => 'switcher',
				'title'      => __( 'Swipe to Slide', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Allow users to drag or swipe directly to a slide irrespective of slidesToScroll.', 'wp-carousel-pro' ),
				'default'    => false,
				'dependency' => array( 'wpcp_carousel_mode', '!=', 'ticker' ),
			),

		),
	)
); // Carousel settings section end.

//
// Style settings section begin.
//
SP_WPCP::createSection(
	$wpcp_carousel_shortcode_settings,
	array(
		'title'  => __( 'Style Settings', 'wp-carousel-pro' ),
		'icon'   => 'fa fa-paint-brush',
		'fields' => array(
			array(
				'id'              => 'wpcp_slide_margin',
				'type'            => 'spacing',
				'title'           => __( 'Space Between', 'wp-carousel-pro' ),
				'subtitle'        => __( 'Set space or margin between the slides. Default value is 20px.', 'wp-carousel-pro' ),
				'all'             => true,
				'all_placeholder' => 'margin',
				'all_text'        => '<i class="fa fa-arrows-h"></i>',
				'unit'            => true,
				'units'           => array( 'px' ),
				'default'         => array(
					'all' => '20',
				),
			),
			array(
				'id'              => 'wpcp_content_carousel_height',
				'type'            => 'dimensions_advanced',
				'title'           => __( 'Slide Height', 'wp-carousel-pro' ),
				'subtitle'        => __( 'Set height for the slides in pixel.', 'wp-carousel-pro' ),
				'top_icon'        => '<i class="fa fa-arrows-v"></i>',
				'top_placeholder' => 'height',
				'right'           => false,
				'bottom'          => false,
				'left'            => false,
				'color'           => false,
				'styles'          => array(
					'min-height',
					'height',
				),
				'default'         => array(
					'top'   => 300,
					'style' => 'min-height',
					'unit'  => 'px',
				),
				'dependency'      => array( 'wpcp_carousel_type', '==', 'content-carousel' ),
			),

			array(
				'id'         => 'wpcp_post_detail_position',
				'type'       => 'select',
				'title'      => __( 'Content Position', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Select a position for the title, content, meta etc.', 'wp-carousel-pro' ),
				'options'    => array(
					'top'          => __( 'Top', 'wp-carousel-pro' ),
					'bottom'       => __( 'Bottom', 'wp-carousel-pro' ),
					'on_right'     => __( 'Right', 'wp-carousel-pro' ),
					'on_left'      => __( 'Left', 'wp-carousel-pro' ),
					'with_overlay' => __( 'Overlay', 'wp-carousel-pro' ),
				),
				'default'    => 'bottom',
				'dependency' => array( 'wpcp_carousel_type', 'any', 'image-carousel,post-carousel,product-carousel' ),
			),
			array(
				'id'         => 'wpcp_overlay_position',
				'type'       => 'radio',
				'title'      => __( 'Overlay Content Type', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set a position for the overlay content', 'wp-carousel-pro' ),
				'inline'     => true,
				'options'    => array(
					'full_covered' => __( 'Fully Covered', 'wp-carousel-pro' ),
					'lower'        => __( 'Caption Style', 'wp-carousel-pro' ),
				),
				'default'    => 'full_covered',
				'dependency' => array( 'wpcp_carousel_type|wpcp_post_detail_position', '==|==', 'image-carousel|with_overlay' ),
			),
			array(
				'id'         => 'wpcp_overlay_visibility',
				'type'       => 'button_set',
				'title'      => __( 'Overlay Content Visibility', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set visibility for the overlay content.', 'wp-carousel-pro' ),
				'options'    => array(
					'always'   => __( 'Always', 'wp-carousel-pro' ),
					'on_hover' => __( 'On Hover', 'wp-carousel-pro' ),
				),
				'radio'      => true,
				'default'    => 'always',
				'dependency' => array( 'wpcp_carousel_type|wpcp_post_detail_position', 'any|==', 'image-carousel,post-carousel,product-carousel|with_overlay' ),
			),
			array(
				'id'         => 'wpcp_overlay_bg',
				'type'       => 'color',
				'title'      => __( 'Overlay Background', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set a color for the overlay background.', 'wp-carousel-pro' ),
				'default'    => 'rgba(0,0,0,0.55)',
				'rgba'       => true,
				'dependency' => array( 'wpcp_carousel_type|wpcp_post_detail_position', 'any|==', 'image-carousel,post-carousel,product-carousel|with_overlay' ),
			),
			array(
				'id'         => 'wpcp_slide_border',
				'type'       => 'border',
				'title'      => __( 'Slide Border', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set border for the slide.', 'wp-carousel-pro' ),
				'all'        => true,
				'default'    => array(
					'all'   => '1',
					'style' => 'solid',
					'color' => '#dddddd',
				),
				'dependency' => array( 'wpcp_carousel_type', '!=', 'product-carousel' ),
			),
			array(
				'id'         => 'wpcp_slide_inner_padding',
				'type'       => 'dimensions_advanced',
				'title'      => __( 'Inner Padding', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set inner padding of the slide. Default value is 0.', 'wp-carousel-pro' ),
				'style'      => false,
				'color'      => false,
				'all'        => true,
				'default'    => array(
					'all' => '0',
				),
				'attributes' => array(
					'min' => 0,
				),
			),
			array(
				'id'         => 'wpcp_slide_background',
				'type'       => 'color',
				'title'      => __( 'Slide Background', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set background color for the slide.', 'wp-carousel-pro' ),
				'default'    => '#f9f9f9',
				'dependency' => array( 'wpcp_carousel_type', 'any', 'image-carousel,post-carousel,video-carousel' ),
			),
			array(
				'id'         => 'wpcp_video_icon_color',
				'type'       => 'color',
				'title'      => __( 'Video Icon Color', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Used as video icon color.', 'wp-carousel-pro' ),
				'default'    => '#fff',
				'dependency' => array( 'wpcp_carousel_type', '==', 'video-carousel' ),
			),
			// Post Settings.
			array(
				'id'         => 'wpcp_post_title',
				'type'       => 'switcher',
				'title'      => __( 'Post Title', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide post title.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Show', 'wp-carousel-pro' ),
				'text_off'   => __( 'Hide', 'wp-carousel-pro' ),
				'text_width' => 77,
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
			),
			array(
				'id'         => 'wpcp_image_caption',
				'type'       => 'switcher',
				'title'      => __( 'Caption', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide image caption.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Show', 'wp-carousel-pro' ),
				'text_off'   => __( 'Hide', 'wp-carousel-pro' ),
				'text_width' => 77,
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type', '==', 'image-carousel' ),
			),
			array(
				'id'         => 'wpcp_image_desc',
				'type'       => 'switcher',
				'title'      => __( 'Description', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide description.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Show', 'wp-carousel-pro' ),
				'text_off'   => __( 'Hide', 'wp-carousel-pro' ),
				'text_width' => 77,
				'default'    => false,
				'dependency' => array( 'wpcp_carousel_type', 'any', 'image-carousel,video-carousel' ),
			),
			array(
				'id'         => 'wpcp_post_content_show',
				'type'       => 'switcher',
				'title'      => __( 'Post Content', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide post content.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Show', 'wp-carousel-pro' ),
				'text_off'   => __( 'Hide', 'wp-carousel-pro' ),
				'text_width' => 77,
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
			),
			array(
				'id'         => 'wpcp_post_content_type',
				'type'       => 'select',
				'title'      => __( 'Content Display Type', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Select a content display type.', 'wp-carousel-pro' ),
				'options'    => array(
					'excerpt'            => __( 'Excerpt', 'wp-carousel-pro' ),
					'content'            => __( 'Full Content', 'wp-carousel-pro' ),
					'content_with_limit' => __( 'Content with Limit', 'wp-carousel-pro' ),
				),
				'default'    => 'content_with_limit',
				'dependency' => array( 'wpcp_carousel_type|wpcp_post_content_show', '==|==', 'post-carousel|true' ),
			),
			array(
				'id'         => 'wpcp_post_content_words_limit',
				'type'       => 'spinner',
				'title'      => __( 'Post Content Words Limit', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set post content words limit. Default value is 30 words.', 'wp-carousel-pro' ),
				'default'    => 30,
				'min'        => 0,
				'dependency' => array( 'wpcp_carousel_type|wpcp_post_content_show|wpcp_post_content_type', '==|==|==', 'post-carousel|true|content_with_limit' ),
			),
			array(
				'id'         => 'wpcp_post_readmore_button_show',
				'type'       => 'switcher',
				'title'      => __( 'Read More Button', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide content read more button.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Show', 'wp-carousel-pro' ),
				'text_off'   => __( 'Hide', 'wp-carousel-pro' ),
				'text_width' => 77,
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type|wpcp_post_content_type', '==|any', 'post-carousel|content_with_limit,excerpt' ),
			),
			array(
				'id'         => 'wpcp_post_readmore_text',
				'type'       => 'text',
				'title'      => __( 'Read More Button Label', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Change the read more button label text.', 'wp-carousel-pro' ),
				'default'    => 'Read More',
				'dependency' => array( 'wpcp_carousel_type|wpcp_post_content_type|wpcp_post_readmore_button_show', '==|any|==', 'post-carousel|content_with_limit,excerpt|true' ),
			),
			array(
				'id'         => 'wpcp_readmore_color_set',
				'type'       => 'color_group',
				'title'      => __( 'Read More Color', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set color for the read more button.', 'wp-carousel-pro' ),
				'options'    => array(
					'color1' => __( 'Color', 'wp-carousel-pro' ),
					'color2' => __( 'Hover Color', 'wp-carousel-pro' ),
					'color3' => __( 'Background', 'wp-carousel-pro' ),
					'color4' => __( 'Hover Background', 'wp-carousel-pro' ),
					'color5' => __( 'Border', 'wp-carousel-pro' ),
					'color6' => __( 'Hover Border', 'wp-carousel-pro' ),
				),
				'default'    => array(
					'color1' => '#fff',
					'color2' => '#fff',
					'color3' => '#22afba',
					'color4' => '#22afba',
					'color5' => '#22afba',
					'color6' => '#22afba',
				),
				'dependency' => array( 'wpcp_carousel_type|wpcp_post_content_type|wpcp_post_readmore_button_show', '==|any|==', 'post-carousel|content_with_limit,excerpt|true' ),
			),
			array(
				'type'       => 'subheading',
				'content'    => __( 'Post Meta', 'wp-carousel-pro' ),
				'dependency' => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
			),
			array(
				'id'         => 'wpcp_post_category_show',
				'type'       => 'switcher',
				'title'      => __( 'Category', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide post category name.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Show', 'wp-carousel-pro' ),
				'text_off'   => __( 'Hide', 'wp-carousel-pro' ),
				'text_width' => 77,
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
			),
			array(
				'id'         => 'wpcp_post_date_show',
				'type'       => 'switcher',
				'title'      => __( 'Date', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide post date.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Show', 'wp-carousel-pro' ),
				'text_off'   => __( 'Hide', 'wp-carousel-pro' ),
				'text_width' => 77,
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
			),
			array(
				'id'         => 'wpcp_post_author_show',
				'type'       => 'switcher',
				'title'      => __( 'Author', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide post author name.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Show', 'wp-carousel-pro' ),
				'text_off'   => __( 'Hide', 'wp-carousel-pro' ),
				'text_width' => 77,
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
			),

			array(
				'id'         => 'wpcp_post_tags_show',
				'type'       => 'switcher',
				'title'      => __( 'Tag', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide post tags.', 'wp-carousel-pro' ),
				'default'    => true,
				'text_on'    => __( 'Show', 'wp-carousel-pro' ),
				'text_off'   => __( 'Hide', 'wp-carousel-pro' ),
				'text_width' => 77,
				'dependency' => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
			),

			array(
				'id'         => 'wpcp_post_comment_show',
				'type'       => 'switcher',
				'title'      => __( 'Comment', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide post comment number.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Show', 'wp-carousel-pro' ),
				'text_off'   => __( 'Hide', 'wp-carousel-pro' ),
				'text_width' => 77,
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
			),

			// Product.
			array(
				'type'       => 'subheading',
				'content'    => __( 'Product', 'wp-carousel-pro' ),
				'dependency' => array( 'wpcp_carousel_type', '==', 'product-carousel' ),
			),
			array(
				'id'         => 'wpcp_product_name',
				'type'       => 'switcher',
				'title'      => __( 'Product Name', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide product name.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Show', 'wp-carousel-pro' ),
				'text_off'   => __( 'Hide', 'wp-carousel-pro' ),
				'text_width' => 77,
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type', '==', 'product-carousel' ),
			),
			array(
				'id'         => 'wpcp_product_price',
				'type'       => 'switcher',
				'title'      => __( 'Product Price', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide product price.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Show', 'wp-carousel-pro' ),
				'text_off'   => __( 'Hide', 'wp-carousel-pro' ),
				'text_width' => 77,
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type', '==', 'product-carousel' ),
			),
			array(
				'id'         => 'wpcp_product_desc',
				'type'       => 'switcher',
				'title'      => __( 'Product Description', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide product description.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Show', 'wp-carousel-pro' ),
				'text_off'   => __( 'Hide', 'wp-carousel-pro' ),
				'text_width' => 77,
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type', '==', 'product-carousel' ),
			),
			array(
				'id'         => 'wpcp_limit_product_desc',
				'type'       => 'switcher',
				'title'      => __( 'Limit Product Description', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Enable/Disable product description limit option.', 'wp-carousel-pro' ),
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type|wpcp_product_desc', '==|==', 'product-carousel|true' ),
			),
			array(
				'id'         => 'wpcp_product_desc_limit_number',
				'type'       => 'spinner',
				'title'      => __( 'Description Words Count', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Number of words to show as the product description. Default value is 15 words.', 'wp-carousel-pro' ),
				'default'    => '15',
				'min'        => 0,
				'dependency' => array( 'wpcp_carousel_type|wpcp_product_desc|wpcp_limit_product_desc', '==|==|==', 'product-carousel|true|true' ),
			),
			array(
				'id'         => 'wpcp_product_readmore_show',
				'type'       => 'switcher',
				'title'      => __( 'Read More', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide product content read more button.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Show', 'wp-carousel-pro' ),
				'text_off'   => __( 'Hide', 'wp-carousel-pro' ),
				'text_width' => 77,
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type|wpcp_product_desc', '==|==', 'product-carousel|true' ),
			),
			array(
				'id'         => 'wpcp_product_readmore_text',
				'type'       => 'text',
				'title'      => __( 'Read More Label', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Write text for the read more button.', 'wp-carousel-pro' ),
				'default'    => 'Read More',
				'dependency' => array( 'wpcp_carousel_type|wpcp_product_desc|wpcp_product_readmore_show', '==|==|==', 'product-carousel|true|true' ),
			),
			array(
				'id'         => 'wpcp_product_rating',
				'type'       => 'switcher',
				'title'      => __( 'Product Rating', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide product rating.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Show', 'wp-carousel-pro' ),
				'text_off'   => __( 'Hide', 'wp-carousel-pro' ),
				'text_width' => 77,
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type', '==', 'product-carousel' ),
			),
			array(
				'id'         => 'wpcp_product_rating_star_color_set',
				'type'       => 'color_group',
				'title'      => __( 'Rating Star Color', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set color for the rating stars.', 'wp-carousel-pro' ),
				'color1'     => true,
				'color2'     => true,
				'options'    => array(
					'color1' => __( 'Color', 'wp-carousel-pro' ),
					'color2' => __( 'Empty Color', 'wp-carousel-pro' ),
				),
				'default'    => array(
					'color1' => '#e74c3c',
					'color2' => '#e74c3c',
				),
				'dependency' => array( 'wpcp_carousel_type|wpcp_product_rating', '==|==', 'product-carousel|true' ),
			),
			array(
				'id'         => 'wpcp_product_rating_alignment',
				'type'       => 'button_set',
				'title'      => __( 'Rating Alignment', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set alignment for the product rating star.', 'wp-carousel-pro' ),
				'class'      => 'wpcp_rating_alignment',
				'options'    => array(
					'left'   => '<i class="fa fa-align-left" title="Left"></i>',
					'center' => '<i class="fa fa-align-center" title="Center"></i>',
					'right'  => '<i class="fa fa-align-right" title="Right"></i>',
				),
				'radio'      => true,
				'default'    => 'center',
				'dependency' => array( 'wpcp_carousel_type|wpcp_product_rating', '==|==', 'product-carousel|true' ),
			),
			array(
				'id'         => 'wpcp_product_cart',
				'type'       => 'switcher',
				'title'      => __( 'Add to Cart Button', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide add to cart button.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Show', 'wp-carousel-pro' ),
				'text_off'   => __( 'Hide', 'wp-carousel-pro' ),
				'text_width' => 77,
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type', '==', 'product-carousel' ),
			),
			array(
				'id'         => 'wpcp_add_to_cart_color_set',
				'type'       => 'color_group',
				'title'      => __( 'Add to Cart Color', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set color for the add to cart button.', 'wp-carousel-pro' ),
				'options'    => array(
					'color1' => __( 'Color', 'wp-carousel-pro' ),
					'color2' => __( 'Hover Color', 'wp-carousel-pro' ),
					'color3' => __( 'Background', 'wp-carousel-pro' ),
					'color4' => __( 'Hover Background', 'wp-carousel-pro' ),
					'color5' => __( 'Border', 'wp-carousel-pro' ),
					'color6' => __( 'Hover Border', 'wp-carousel-pro' ),
				),
				'default'    => array(
					'color1' => '#545454',
					'color2' => '#fff',
					'color3' => '#ebebeb',
					'color4' => '#3f3f3f',
					'color5' => '#d1d1d1',
					'color6' => '#d1d1d1',
				),
				'dependency' => array( 'wpcp_carousel_type|wpcp_product_cart', '==|==', 'product-carousel|true' ),
			),
			// Image.
			array(
				'type'       => 'subheading',
				'content'    => __( 'Image', 'wp-carousel-pro' ),
				'dependency' => array( 'wpcp_carousel_type', '!=', 'content-carousel' ),
			),
			array(
				'id'         => 'show_image',
				'type'       => 'switcher',
				'title'      => __( 'Image', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Show/Hide slide image.', 'wp-carousel-pro' ),
				'text_on'    => __( 'Show', 'wp-carousel-pro' ),
				'text_off'   => __( 'Hide', 'wp-carousel-pro' ),
				'text_width' => 77,
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type', 'any', 'post-carousel,product-carousel' ),
			),
			array(
				'id'         => 'wpcp_image_sizes',
				'type'       => 'image_sizes',
				'chosen'     => true,
				'title'      => __( 'Image Sizes', 'wp-carousel-pro' ),
				'default'    => 'full',
				'subtitle'   => __( 'Select a image size.', 'wp-carousel-pro' ),
				'dependency' => array( 'wpcp_carousel_type|show_image', 'any|==', 'image-carousel,post-carousel,product-carousel,video-carousel|true' ),
			),
			array(
				'id'                => 'wpcp_image_crop_size',
				'type'              => 'dimensions_advanced',
				'title'             => __( 'Custom Size', 'wp-carousel-pro' ),
				'subtitle'          => __( 'Set width and height of the image.', 'wp-carousel-pro' ),
				'chosen'            => true,
				'bottom'            => false,
				'left'              => false,
				'color'             => false,
				'top_icon'          => '<i class="fa fa-arrows-h"></i>',
				'right_icon'        => '<i class="fa fa-arrows-v"></i>',
				'top_placeholder'   => 'width',
				'right_placeholder' => 'height',
				'styles'            => array(
					'Soft-crop',
					'Hard-crop',
				),
				'default'           => array(
					'top'   => '600',
					'right' => '400',
					'style' => 'Soft-crop',
					'unit'  => 'px',
				),
				'attributes'        => array(
					'min' => 0,
				),
				'dependency'        => array( 'wpcp_carousel_type|show_image|wpcp_image_sizes', 'any|==|==', 'image-carousel,post-carousel,product-carousel,video-carousel|true|custom', true ),
			),
			array(
				'id'                 => 'wpcp_image_height_css',
				'type'               => 'dimensions_advanced',
				'title'              => __( 'Image Height CSS', 'wp-carousel-pro' ),
				'subtitle'           => __( 'Set image height on different devices for perfect responsive in px.', 'wp-carousel-pro' ),
				'top_icon'           => '<i class="fa fa-desktop"></i>',
				'right_icon'         => '<i class="fa fa-laptop"></i>',
				'bottom_icon'        => '<i class="fa fa-tablet"></i>',
				'left_icon'          => '<i class="fa fa-mobile"></i>',
				'top_placeholder'    => 'height',
				'right_placeholder'  => 'height',
				'bottom_placeholder' => 'height',
				'left_placeholder'   => 'height',
				'color'              => false,
				'styles'             => array(
					'max-height',
					'height',
				),
				'default'            => array(
					'top'    => 300,
					'right'  => 300,
					'bottom' => 200,
					'left'   => 180,
					'style'  => 'max-height',
					'unit'   => 'px',
				),
				'dependency'         => array( 'wpcp_carousel_type|show_image', 'any|==', 'image-carousel,post-carousel,product-carousel,video-carousel|true' ),
			),
			array(
				'id'         => 'wpcp_product_image_border',
				'type'       => 'border',
				'title'      => __( 'Image Border', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set border for the product image.', 'wp-carousel-pro' ),
				'all'        => true,
				'default'    => array(
					'all'   => '1',
					'style' => 'solid',
					'color' => '#dddddd',
				),
				'dependency' => array( 'wpcp_carousel_type', '==', 'product-carousel' ),
			),
			array(
				'id'              => 'wpcp_image_border_radius',
				'type'            => 'dimensions_advanced',
				'title'           => __( 'Border Radius', 'wp-carousel-pro' ),
				'subtitle'        => __( 'Set border radius for the carousel image. Square size image fits well.', 'wp-carousel-pro' ),
				'all_placeholder' => 'all sides',
				'unit'            => false,
				'color'           => false,
				'all'             => true,
				'styles'          => array(
					'px',
					'%',
				),
				'default'         => array(
					'all'   => '0',
					'style' => 'px',
				),
				'dependency'      => array( 'wpcp_carousel_type', '!=', 'content-carousel' ),
			),
			array(
				'id'         => '_variable_width',
				'type'       => 'switcher',
				'title'      => __( 'Variable Width', 'wp-carousel-pro' ),
				'subtitle'   => __( 'On/Off variable width. Number of column(s) depends on image width.', 'wp-carousel-pro' ),
				'default'    => false,
				'dependency' => array( 'wpcp_carousel_mode|wpcp_carousel_type', '!=|!=', 'ticker|content-carousel' ),
			),
			array(
				'id'         => '_image_light_box',
				'type'       => 'switcher',
				'title'      => __( 'Lightbox', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Enable/Disable lightbox for the image.', 'wp-carousel-pro' ),
				'default'    => false,
				'dependency' => array( 'wpcp_carousel_type|show_image', 'any|==', 'image-carousel,product-carousel|true' ),
			),
			array(
				'id'         => 'wpcp_image_lazy_load',
				'type'       => 'button_set',
				'title'      => __( 'Lazy Load', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set lazy load option for the image.', 'wp-carousel-pro' ),
				'options'    => array(
					'false'    => __( 'Off', 'wp-carousel-pro' ),
					'ondemand' => __( 'On Demand', 'wp-carousel-pro' ),
				),
				'attributes' => array(
					'data-depend-id' => 'wpcp_image_lazy_load',
				),
				'radio'      => true,
				'default'    => 'false',
				'dependency' => array( 'wpcp_carousel_type|wpcp_carousel_mode|show_image', 'any|!=|==', 'image-carousel,post-carousel,product-carousel|ticker|true' ),
			),
			array(
				'id'         => 'wpcp_image_zoom',
				'type'       => 'select',
				'title'      => __( 'Zoom', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set a zoom effect on hover the image.', 'wp-carousel-pro' ),
				'options'    => array(
					''         => __( 'Off', 'wp-carousel-pro' ),
					'zoom_in'  => __( 'Zoom In', 'wp-carousel-pro' ),
					'zoom_out' => __( 'Zoom Out', 'wp-carousel-pro' ),
				),
				'default'    => '',
				'class'      => 'chosen',
				'dependency' => array( 'wpcp_carousel_type|show_image', 'any|==', 'image-carousel,post-carousel,product-carousel|true' ),
			),
			array(
				'id'       => 'wpcp_image_gray_scale',
				'type'     => 'select',
				'title'    => __( 'GrayScale', 'wp-carousel-pro' ),
				'subtitle' => __( 'Set a grayscale effect for the image.', 'wp-carousel-pro' ),
				'options'  => array(
					''                         => __( 'Off', 'wp-carousel-pro' ),
					'gray_and_normal_on_hover' => __( 'Gray and normal on hover', 'wp-carousel-pro' ),
					'gray_on_hover'            => __( 'Gray on hover', 'wp-carousel-pro' ),
					'always_gray'              => __( 'Always Gray', 'wp-carousel-pro' ),
				),
				'default'  => '',
				'class'    => 'chosen',
			),
			array(
				'id'         => '_image_title_attr',
				'type'       => 'checkbox',
				'title'      => __( 'Image Title Attribute', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Check to add image title attribute.', 'wp-carousel-pro' ),
				'default'    => false,
				'dependency' => array( 'wpcp_carousel_type|show_image', 'any|==', 'image-carousel,post-carousel,product-carousel|true' ),
			),
		), // End of fields array.
	)
); // Style settings section end.


//
// Typography section begin.
//
SP_WPCP::createSection(
	$wpcp_carousel_shortcode_settings,
	array(
		'title'           => __( 'Typography', 'wp-carousel-pro' ),
		'icon'            => 'fa fa-font',
		'enqueue_webfont' => true,
		'fields'          => array(
			array(
				'id'       => 'section_title_font_load',
				'type'     => 'switcher',
				'title'    => __( 'Load Carousel Section Title Font', 'wp-carousel-pro' ),
				'subtitle' => __( 'On/Off google font for the carousel section title.', 'wp-carousel-pro' ),
				'default'  => true,
			),
			array(
				'id'           => 'wpcp_section_title_typography',
				'type'         => 'typography',
				'title'        => __( 'Carousel Section Title Font', 'wp-carousel-pro' ),
				'subtitle'     => __( 'Set Carousel section title font properties.', 'wp-carousel-pro' ),
				'default'      => array(
					'color'          => '#444444',
					'font-family'    => 'Open Sans',
					'font-weight'    => '600',
					'font-size'      => '24',
					'line-height'    => '28',
					'letter-spacing' => '0',
					'text-align'     => 'center',
					'text-transform' => 'none',
					'type'           => 'google',
					'unit'           => 'px',
				),
				'preview'      => 'always',
				'preview_text' => 'Carousel Section Title',
			),
			array(
				'id'         => 'wpcp_image_caption_font_load',
				'type'       => 'switcher',
				'title'      => __( 'Load Caption Font', 'wp-carousel-pro' ),
				'subtitle'   => __( 'On/Off google font for the image caption.', 'wp-carousel-pro' ),
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type', '==', 'image-carousel' ),
			),
			array(
				'id'           => 'wpcp_image_caption_typography',
				'type'         => 'typography',
				'title'        => __( 'Caption Font', 'wp-carousel-pro' ),
				'subtitle'     => __( 'Set caption font properties.', 'wp-carousel-pro' ),
				'default'      => array(
					'color'          => '#333',
					'font-family'    => 'Open Sans',
					'font-weight'    => '600',
					'font-size'      => '15',
					'line-height'    => '23',
					'letter-spacing' => '0',
					'text-align'     => 'center',
					'text-transform' => 'capitalize',
					'type'           => 'google',
				),
				'preview_text' => 'The image caption',
				'dependency'   => array( 'wpcp_carousel_type', '==', 'image-carousel', true ),
			),
			array(
				'id'         => 'wpcp_image_desc_font_load',
				'type'       => 'switcher',
				'title'      => __( 'Load Description Font', 'wp-carousel-pro' ),
				'subtitle'   => __( 'On/Off google font for the image description.', 'wp-carousel-pro' ),
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type|wpcp_post_title', '==|==', 'image-carousel|true' ),
			),
			array(
				'id'         => 'wpcp_image_desc_typography',
				'type'       => 'typography',
				'title'      => __( 'Description Font', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set description font properties.', 'wp-carousel-pro' ),
				'default'    => array(
					'color'          => '#333',
					'font-family'    => 'Open Sans',
					'font-weight'    => '400',
					'font-style'     => 'normal',
					'font-size'      => '14',
					'line-height'    => '21',
					'letter-spacing' => '0',
					'text-align'     => 'center',
					'type'           => 'google',
				),
				'dependency' => array( 'wpcp_carousel_type', '==', 'image-carousel' ),
			),
			// Post Typography.
			array(
				'id'         => 'wpcp_title_font_load',
				'type'       => 'switcher',
				'title'      => __( 'Load Title Font', 'wp-carousel-pro' ),
				'subtitle'   => __( 'On/Off google font for the slide title.', 'wp-carousel-pro' ),
				'default'    => true,
				'dependency' => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
			),
			array(
				'id'           => 'wpcp_title_typography',
				'type'         => 'typography',
				'title'        => __( 'Post Title Font', 'wp-carousel-pro' ),
				'subtitle'     => __( 'Set title font properties.', 'wp-carousel-pro' ),
				'default'      => array(
					'color'          => '#444',
					'hover_color'    => '#555',
					'font-family'    => 'Open Sans',
					'font-style'     => '600',
					'font-size'      => '20',
					'line-height'    => '30',
					'letter-spacing' => '0',
					'text-align'     => 'center',
					'text-transform' => 'capitalize',
					'type'           => 'google',
				),
				'hover_color'  => true,
				'preview_text' => 'The Post Title',
				'dependency'   => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
			),

			array(
				'id'         => 'wpcp_post_content_font_load',
				'type'       => 'switcher',
				'title'      => __( 'Post Content Font Load', 'wp-carousel-pro' ),
				'subtitle'   => __( 'On/Off google font for post the content.', 'wp-carousel-pro' ),
				'default'    => true,
				'dependency' => array(
					'wpcp_carousel_type',
					'==',
					'post-carousel',
				),
			),
			array(
				'id'         => 'wpcp_post_content_typography',
				'type'       => 'typography',
				'title'      => __( 'Post Content Font', 'wp-carousel-pro' ),
				'subtitle'   => __( 'Set post content font properties.', 'wp-carousel-pro' ),
				'default'    => array(
					'color'          => '#333',
					'font-family'    => 'Open Sans',
					'font-style'     => '400',
					'font-size'      => '16',
					'line-height'    => '26',
					'letter-spacing' => '0',
					'text-align'     => 'center',
					'type'           => 'google',
				),
				'dependency' => array( 'wpcp_carousel_type', '==', 'post-carousel' ),
			),

			array(
				'id'         => 'wpcp_post_cat_font_load',
				'type'       => 'switcher',
				'title'      => __( 'Post Category Font Load', 'wp-carousel-pro' ),
				'subtitle'   => __( 'On/Off google font for the post category.', 'wp-carousel-pro' ),
				'default'    => true,
				'dependency' => array(
					'wpcp_carousel_type',
					'==',
					'post-carousel',
				),
			),
			array(
				'id'           => 'wpcp_post_cat_typography',
				'type'         => 'typography',
				'title'        => __( 'Post Category Font', 'wp-carousel-pro' ),
				'subtitle'     => __( 'Set post category font properties.', 'wp-carousel-pro' ),
				'default'      => array(
					'color'          => '#22afba',
					'font-family'    => 'Open Sans',
					'font-style'     => '600',
					'font-size'      => '14',
					'line-height'    => '21',
					'letter-spacing' => '0',
					'text-align'     => 'center',
					'type'           => 'google',
				),
				'preview_text' => 'The Category', // Replace preview text with any text you like.
				'dependency'   => array(
					'wpcp_carousel_type',
					'==',
					'post-carousel',
				),
			),
			array(
				'id'         => 'wpcp_post_meta_font_load',
				'type'       => 'switcher',
				'title'      => __( 'Post Meta Font Load', 'wp-carousel-pro' ),
				'subtitle'   => __( 'On/Off google font for the post meta.', 'wp-carousel-pro' ),
				'default'    => true,
				'dependency' => array(
					'wpcp_carousel_type',
					'==',
					'post-carousel',
				),
			),
			array(
				'id'           => 'wpcp_post_meta_typography',
				'type'         => 'typography',
				'title'        => __( 'Post Meta Font', 'wp-carousel-pro' ),
				'subtitle'     => __( 'Set post meta font properties.', 'wp-carousel-pro' ),
				'default'      => array(
					'color'          => '#999',
					'font-family'    => 'Open Sans',
					'font-style'     => '400',
					'font-size'      => '14',
					'line-height'    => '24',
					'letter-spacing' => '0',
					'text-align'     => 'center',
					'type'           => 'google',
				),
				'preview_text' => 'Post Meta', // Replace preview text with any text you like.
				'dependency'   => array(
					'wpcp_carousel_type',
					'==',
					'post-carousel',
				),
			),
			array(
				'id'         => 'wpcp_post_readmore_font_load',
				'type'       => 'switcher',
				'title'      => __( 'Read More Font Load', 'wp-carousel-pro' ),
				'subtitle'   => __( 'On/Off google font for the read more.', 'wp-carousel-pro' ),
				'default'    => true,
				'dependency' => array(
					'wpcp_carousel_type',
					'==',
					'post-carousel',
				),
			),
			array(
				'id'           => 'wpcp_post_readmore_typography',
				'type'         => 'typography',
				'title'        => __( 'Read More Font', 'wp-carousel-pro' ),
				'subtitle'     => __( 'Set read more font properties.', 'wp-carousel-pro' ),
				'default'      => array(
					'font-family'    => 'Open Sans',
					'font-style'     => '600',
					'font-size'      => '14',
					'line-height'    => '24',
					'letter-spacing' => '0',
					'text-align'     => 'center',
					'type'           => 'google',
				),
				'color'        => false,
				'preview_text' => 'Read more', // Replace preview text with any text you like.
				'dependency'   => array(
					'wpcp_carousel_type',
					'==',
					'post-carousel',
				),
			),

			// Product Typography.
			array(
				'id'         => 'wpcp_product_name_font_load',
				'type'       => 'switcher',
				'title'      => __( 'Product Name Font Load', 'wp-carousel-pro' ),
				'subtitle'   => __( 'On/Off google font for the product name.', 'wp-carousel-pro' ),
				'default'    => true,
				'dependency' => array(
					'wpcp_carousel_type',
					'==',
					'product-carousel',
				),
			),
			array(
				'id'           => 'wpcp_product_name_typography',
				'type'         => 'typography',
				'title'        => __( 'Product Name Font', 'wp-carousel-pro' ),
				'subtitle'     => __( 'Set product name font properties.', 'wp-carousel-pro' ),
				'default'      => array(
					'color'          => '#444',
					'hover_color'    => '#555',
					'font-family'    => 'Open Sans',
					'font-style'     => '400',
					'font-size'      => '15',
					'line-height'    => '23',
					'letter-spacing' => '0',
					'text-align'     => 'center',
					'type'           => 'google',
				),
				'hover_color'  => true,
				'preview_text' => 'Product Name', // Replace preview text.
				'dependency'   => array(
					'wpcp_carousel_type',
					'==',
					'product-carousel',
				),
			),
			array(
				'id'         => 'wpcp_product_desc_font_load',
				'type'       => 'switcher',
				'title'      => __( 'Product Description Font Load', 'wp-carousel-pro' ),
				'subtitle'   => __( 'On/Off google font for the product description.', 'wp-carousel-pro' ),
				'default'    => true,
				'dependency' => array(
					'wpcp_carousel_type',
					'==',
					'product-carousel',
				),
			),
			array(
				'id'           => 'wpcp_product_desc_typography',
				'type'         => 'typography',
				'title'        => __( 'Product Description Font', 'wp-carousel-pro' ),
				'subtitle'     => __( 'Set product description font properties.', 'wp-carousel-pro' ),
				'default'      => array(
					'color'          => '#333',
					'font-family'    => 'Open Sans',
					'font-style'     => '400',
					'font-size'      => '14',
					'line-height'    => '22',
					'letter-spacing' => '0',
					'text-align'     => 'center',
					'type'           => 'google',
				),
				'preview_text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', // Replace preview text with any text you like.
				'dependency'   => array(
					'wpcp_carousel_type',
					'==',
					'product-carousel',
				),
			),
			array(
				'id'         => 'wpcp_product_price_font_load',
				'type'       => 'switcher',
				'title'      => __( 'Product Price Font Load', 'wp-carousel-pro' ),
				'subtitle'   => __( 'On/Off google font for the product price.', 'wp-carousel-pro' ),
				'default'    => true,
				'dependency' => array(
					'wpcp_carousel_type',
					'==',
					'product-carousel',
				),
			),
			array(
				'id'           => 'wpcp_product_price_typography',
				'type'         => 'typography',
				'title'        => __( 'Product Price Font', 'wp-carousel-pro' ),
				'subtitle'     => __( 'Set product price font properties.', 'wp-carousel-pro' ),
				'default'      => array(
					'color'          => '#222',
					'font-family'    => 'Open Sans',
					'font-style'     => '700',
					'font-size'      => '14',
					'line-height'    => '26',
					'letter-spacing' => '0',
					'text-align'     => 'center',
					'type'           => 'google',
				),
				'preview_text' => '$49.00', // Replace preview text with any text you like.
				'dependency'   => array(
					'wpcp_carousel_type',
					'==',
					'product-carousel',
				),
			),
			array(
				'id'         => 'wpcp_product_readmore_font_load',
				'type'       => 'switcher',
				'title'      => __( 'Read More Font Load', 'wp-carousel-pro' ),
				'subtitle'   => __( 'On/Off google font for the read more.', 'wp-carousel-pro' ),
				'default'    => true,
				'dependency' => array(
					'wpcp_carousel_type',
					'==',
					'product-carousel',
				),
			),
			array(
				'id'           => 'wpcp_product_readmore_typography',
				'type'         => 'typography',
				'title'        => __( 'Read More Font', 'wp-carousel-pro' ),
				'subtitle'     => __( 'Set read more font properties.', 'wp-carousel-pro' ),
				'default'      => array(
					'color'          => '#e74c3c',
					'hover_color'    => '#e74c3c',
					'font-family'    => 'Open Sans',
					'font-style'     => '600',
					'font-size'      => '14',
					'line-height'    => '24',
					'letter-spacing' => '0',
					'text-align'     => 'center',
					'type'           => 'google',
				),
				'hover_color'  => true,
				'preview_text' => 'Read more', // Replace preview text with any text you like.
				'dependency'   => array(
					'wpcp_carousel_type',
					'==',
					'product-carousel',
				),
			),
		), // End of fields array.
	)
); // Style settings section end.


//
// Metabox of the footer section / shortocde section.
// Set a unique slug-like ID.
//
$wpcp_display_shortcode = 'sp_wpcp_display_shortcode';

//
// Create a metabox.
//
SP_WPCP::createMetabox(
	$wpcp_display_shortcode,
	array(
		'title'        => __( 'WordPress Carousel Pro', 'wp-carousel-pro' ),
		'post_type'    => 'sp_wp_carousel',
		'show_restore' => false,
	)
);

//
// Create a section.
//
SP_WPCP::createSection(
	$wpcp_display_shortcode,
	array(
		'fields' => array(
			array(
				'type'  => 'shortcode',
				'class' => 'wpcp-admin-footer',
			),
		),
	)
);
