<?php
/**
 * This file is responsible for creating calendar layout for events
 */
class ect_calendar_template{

	function __construct(){
		add_action( 'admin_enqueue_scripts', array($this, 'event_cat_colorpicker_enqueue' ) );
		add_action( 'tribe_events_cat_add_form_fields', array( $this, 'event_category_add_style'), 100, 1 );
		add_action( 'tribe_events_cat_edit_form_fields', array( $this, 'event_category_add_style'), 100, 1 );
		add_action( 'admin_print_scripts', array($this, 'event_cat_colorpicker_init'), 100 );

		add_action( 'created_tribe_events_cat', array($this, 'event_save_category_style') );  // Variable Hook Name
		add_action( 'edited_tribe_events_cat',  array($this,  'event_save_category_style') );  // Variable Hook Name

		add_action('wp_enqueue_scripts', array( $this, 'ect_register_script'));
		add_shortcode('ect-calendar-layout', array( $this, 'ect_calendar_shortcode' ) );
		add_filter( 'tribe_rest_event_data', array( $this, 'ect_filter_events_rest_data'), 10,2 );
	}


	/*
	|---------------------------------------------|
	|   Enqueue all required CSS and JS           |
	|---------------------------------------------|
	*/
	function ect_register_script(){
	
		wp_register_script('ect-moment', ECT_PRO_PLUGIN_URL.'assets/js/moment.min.js',null,ECT_VERSION_CURRENT,false);

		wp_register_script('ect-code-snippet', ECT_PRO_PLUGIN_URL.'assets/js/tui-code-snippet.js',null,ECT_VERSION_CURRENT,false);
		wp_register_script('ect-calendar', ECT_PRO_PLUGIN_URL.'assets/js/calendar.js',null,ECT_VERSION_CURRENT,false);
		wp_register_style('ect_calendar-style', ECT_PRO_PLUGIN_URL. 'assets/css/tui-calendar.css', array(), ECT_VERSION_CURRENT, 'all');
		wp_register_style('ect-common-styles', ECT_PRO_PLUGIN_URL. 'assets/css/ect-common-styles.css', array(), ECT_VERSION_CURRENT, 'all');
		wp_register_style('ect_custom-style', ECT_PRO_PLUGIN_URL. 'assets/css/ect-custom-calendar.css', array(), ECT_VERSION_CURRENT, 'all');
		wp_register_script('ect_custom', ECT_PRO_PLUGIN_URL. 'assets/js/ect-custom-calendar.js', array('jquery','wp-api-request'), ECT_VERSION_CURRENT, true);

		$titan = TitanFramework::getInstance( 'ect' );
		$event_bgColor = $titan->getOption( 'ect_calendar_bgcolor' );
		$event_textColor = $titan->getOption( 'ect_calendar_text_color' );

		wp_localize_script( 'ect_custom', 'event_api' , array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'ect_cal_bgColor'=> $event_bgColor,
			'ect_cal_textColor' => $event_textColor
			)
		);
	}


	/*
	|------------------------------------------------|
	|   Shortcode for generating calendar layout     |
	|------------------------------------------------|
	*/
	public function ect_calendar_shortcode( $attr, $content=null){
		$attr = shortcode_atts(
			array(
				'id'=>'',
				'date-format'=>'d F Y',
				'show-category-filter'=>'true',
				//'time-format'=>'hh:mm A',
		), $attr, 'ect_CAL');
		
			wp_enqueue_script( 'ect-moment' );
			wp_enqueue_script( 'ect-code-snippet' );
			wp_enqueue_script( 'ect-calendar' );
			wp_enqueue_script( 'ect_custom' );
			wp_enqueue_style( 'ect_calendar-style' );
			wp_enqueue_style( 'ect-common-styles' );
			wp_enqueue_style( 'ect_custom-style' );
		$cat_filter = empty( $attr['show-category-filter'] )?'true':$attr['show-category-filter'];
		$date_format = empty($attr['date-format'])?'d F Y':$attr['date-format'];
		// convert PHP date format TO Moment.js format
		$date_format = str_replace( array('F','j','d','m','M','Y','y','l'), array('X','D','DD','C','MMM','YYYY','YY','dddd'),$date_format );
		$date_format = str_replace(array("X","C"),array("MMMM","MM"),$date_format);
		$time_format = empty($attr['time-format'])?'hh:mm A':$attr['time-format'];
        $titan = TitanFramework::getInstance( 'ect' );
        $featured_bgcolor = $titan->getOption( 'featured_event_skin_color');
        $featured_textColor = $titan->getOption( 'featured_event_font_color');
        $main_skin_color = $titan->getOption( 'main_skin_color');
        $alternate_color = $titan->getOption( 'main_skin_alternate_color');
        $TimeStamp = mktime(0,0,0,1,1,date('Y') );
        $start_date = date('Y-m-d',$TimeStamp);
        $ID = $attr['id']==''?uniqid():$attr['id'];
		$nameOfMonths = array(
			__('January','ect'),
			__('February','ect'),
			__('March','ect'),
			__('April','ect'),
			__('May','ect'),
			__('June','ect'),
			__('July','ect'),
			__('August','ect'),
			__('September','ect'),
			__('October','ect'),
			__('November','ect'),
			__('December','ect'),
		);
		$nameOfDays = array(
			__('Sun','ect'),
			__('Mon','ect'),
			__('Tue','ect'),
			__('Wed','ect'),
			__('Thu','ect'),
			__('Fri','ect'),
			__('Sat','ect')
		);
		$monthsName = str_replace(' ', '', $nameOfMonths);
		
		// data-events-start-date="'.$events_start_date.'" data-events-end-date="'.$events_end_date.'"
		$cal ='<div id="ect-calendar-wrapper" class="ect-custom-calendar" data-date-format="'.$date_format.'" data-time-format="'.$time_format.'" data-skin-color="'.$main_skin_color.'" data-alt-skin-color="'.$alternate_color.'" data-events-limit="50" data-calendar-id="'.$ID.'" data-days-name=["'.implode('","',$nameOfDays).'"] data-featured-bgcolor="'.$featured_bgcolor.'" data-featured-textcolor="'.$featured_textColor.'">
		<div class="ect-calendar-menu">';
		if( $cat_filter == 'true' ){
			$cal .='<div class="ect-calendar-cat-filter" data-calendar-id="'.$ID.'"></div>';
		}
		$cal .='<span class="ect-calendar-menu_navi">
		  <button type="button" class="ect-calendar_btn" data-calendar-id="'.$ID.'" data-action="move-today">'.__("Today","ect").'</button>
			<i class="ect-icon-left ect-calendar_btn" data-action="move-prev" data-calendar-id="'.$ID.'"></i>
			<i class="ect-icon-right ect-calendar_btn" data-action="move-next" data-calendar-id="'.$ID.'"></i>
		</span>
		<span class="ect_renderRange" data-calendar-id="'.$ID.'" class="render-range" data-months=["'.implode('","',$monthsName).'"]></span>
		<select class="ect-calendar-select" data-calendar-id="'.$ID.'">
			<option value="month">'.__('Month','ect').'</option>
			<option value="day">'.__("Day","ect").'</option>
			<option value="week">'.__("Week","ect").'</option>
		</select>
	  </div>
	  <div class="ect-calendar-container">
	  <div class="ect_calendar_events_spinner"><img src="'.ECT_PRO_PLUGIN_URL .'assets/images/ect-preloader.gif"></div>
	  <div id="ect_calendar-'.$ID.'"> 
	  </div>
      </div>
      </div>';
		return $cal;
	
	
}

	/*
    |---------------------------------------------------------------------------------------|
    |   The Event Calendar (Tribe) filter to add custom data on event REST api response     |
    |---------------------------------------------------------------------------------------|
    */
	function ect_filter_events_rest_data($data, $event) {
		$event_id = $data['id'];
		$category = $data['categories'];
		$slug = '';
		if( !empty($category) ){
			$slug = $category[0]['term_taxonomy_id'];
		}
		
		$event_bgColor = get_term_meta( $slug, '_event_bgColor', true );
		$event_bgColor = isset($event_bgColor)?'#'.$event_bgColor:'';
		$event_text_color = get_term_meta( $slug, '_event_textColor', true );
		$event_text_color = isset($event_text_color)?'#'.$event_text_color:'';

		$data = array_merge( $data , array(
			'event_bgcolor'  =>  $event_bgColor,
			'event_text_color'  =>  $event_text_color,
			)
		);
		  
			return $data;
	}

	function event_category_add_style( $term ){
		$screen = get_current_screen() ;
		
		if( !isset($_GET['taxonomy']) || $_GET['taxonomy'] != 'tribe_events_cat' ){
			return;
		}

		if( isset($screen->base) && $screen->base == "edit-tags" ){
		?>
		<div class="form-field">
			Background Color <input name="_event_bgColor" value="#" class="colorpicker" id="term-colorpicker" />
			Text Color <input name="_event_textColor" value="#" class="colorpicker" id="term-colorpicker" />
		</div>
		<?php
		}else{
			$bgColor = get_term_meta( $term->term_id, '_event_bgColor', true );
			$textColor = get_term_meta( $term->term_id, '_event_textColor', true );
			
			$bgColor = ( ! empty( $bgColor ) ) ? "#{$bgColor}" : '#';
			$textColor = ( ! empty( $textColor ) ) ? "#{$textColor}" : '#';
			?>
				<tr class="form-field term-colorpicker-wrap">
					<th scope="row"><label for="term-bgcolorpicker">Background Color</label></th>
					<td>
						<input name="_event_bgColor" value="<?php echo $bgColor; ?>" class="colorpicker" id="term-bgcolorpicker" />
						<p class="description">This is the field description where you can tell the user how the color is used in the theme.</p>
					</td>
				</tr>
				<tr class="form-field term-colorpicker-wrap">
					<th scope="row"><label for="term-event_textColor">Text Color</label></th>
					<td>
						<input name="_event_textColor" value="<?php echo $textColor; ?>" class="colorpicker" id="term-event_textColor" />
						<p class="description">This is the field description where you can tell the user how the color is used in the theme.</p>
					</td>
				</tr>
			<?php
		}
	}

	function event_save_category_style( $term_id ) {

		// Save term color if possible
		if( isset( $_POST['_event_bgColor'] ) && ! empty( $_POST['_event_bgColor'] ) ) {
			update_term_meta( $term_id, '_event_bgColor', sanitize_hex_color_no_hash( $_POST['_event_bgColor'] ) );
			
		} else {
			delete_term_meta( $term_id, '_event_bgColor' );
			
		}

		if( isset( $_POST['_event_textColor'] ) && ! empty( $_POST['_event_textColor'] ) ) {
			update_term_meta( $term_id, '_event_textColor', sanitize_hex_color_no_hash( $_POST['_event_textColor'] ) );
		}else{
			delete_term_meta( $term_id, '_event_textColor' );
		}
	}

	function event_cat_colorpicker_enqueue( $taxonomy ) {

		if( null !== ( $screen = get_current_screen() ) && 'edit-tribe_events_cat' !== $screen->id ) {
			return;
		}
	
		// Colorpicker Scripts
		wp_enqueue_script( 'wp-color-picker' );
	
		// Colorpicker Styles
		wp_enqueue_style( 'wp-color-picker' );
	
	}

	function event_cat_colorpicker_init() {
 
		if( null !== ( $screen = get_current_screen() ) && 'edit-tribe_events_cat' != $screen->id ) {
			return;
		}
	
	  ?>
	
		<script>
			jQuery( document ).ready( function( $ ) {
			$( '.colorpicker' ).wpColorPicker();
	
			} ); // End Document Ready JQuery
		</script>
	
	  <?php
	
	}
	
	

}
new ect_calendar_template;