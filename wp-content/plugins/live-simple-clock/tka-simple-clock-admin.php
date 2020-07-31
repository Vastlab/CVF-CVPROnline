<?php

function tka_simple_clock_page() {
	// menu
	add_menu_page( 
		'Live Simple Clock',
		'Live Simple Clock',
		'manage_options',
		'setting_lcs.php',
		'tka_simple_clock_admin_callback',
		'dashicons-clock'
		
		);
	// activate custom setting
	add_action( 'admin_init', 'tka_lsc_custom_setting');
}
add_action('admin_menu', 'tka_simple_clock_page');


function tka_lsc_custom_setting(){
	//Enregistrement Général
	register_setting( 'tka-lsc-setting-group','tka_lsc_title');
	register_setting( 'tka-lsc-setting-group','tka_lsc_format');
	register_setting( 'tka-lsc-setting-group','tka_lsc_fuseau');
	register_setting( 'tka-lsc-setting-group','tka_lsc_hidesecond');
	//Enregistrement css
	register_setting( 'tka-lsc-setting-group','tka_lsc_font');
	register_setting( 'tka-lsc-setting-group','tka_lsc_font_size');
	register_setting( 'tka-lsc-setting-group','tka_lsc_font_color');
	register_setting( 'tka-lsc-setting-group','tka_lsc_font_weight');
	//Section
	add_settings_section( 'tka-lsc-general-option','General Option','tka_lsc_general_option','setting_lcs.php');
	add_settings_section( 'tka-lsc-css-option','Style Option','tka_lsc_css_option','setting_lcs.php');
	//Field general
	add_settings_field( 'tka-general-title','Prefix','tka_general_title','setting_lcs.php','tka-lsc-general-option');
	add_settings_field( 'tka-general-format','Time zone','tka_general_format','setting_lcs.php','tka-lsc-general-option');
	add_settings_field( 'tka-general-fuseau','Fuseau Horaire','tka_general_fuseau','setting_lcs.php','tka-lsc-general-option');
	add_settings_field( 'tka-general-hidesecond','Hide Second','tka_general_hidesecond','setting_lcs.php','tka-lsc-general-option');
	//Field css
	add_settings_field( 'tka-css-font','Font','tka_css_font','setting_lcs.php','tka-lsc-css-option');
	add_settings_field( 'tka-css-font_size','Font Size','tka_css_font_size','setting_lcs.php','tka-lsc-css-option');
	add_settings_field( 'tka-css-font_color','Font Weight','tka_css_font_weight','setting_lcs.php','tka-lsc-css-option');
	add_settings_field( 'tka-css-font_weight','Font Color','tka_css_font_color','setting_lcs.php','tka-lsc-css-option');

}
//Fonction Section
function tka_lsc_css_option(){
	echo 'css information';
}
function tka_lsc_general_option(){
	echo '<h3>Shortcode: <code>[live_simple_clock]</code><code>'.esc_html('<?php echo do_shortcode( \'[live_simple_clock]\' );?>').'</code></h3>';

}
// fonction field css
function tka_css_font(){
	$font = esc_attr( get_option( 'tka_lsc_font' ) );
	echo '<input type="text" name="tka_lsc_font" value="'.$font.'" placeholder="arial" />';
}
function tka_css_font_size(){
	$font_size = esc_attr( get_option( 'tka_lsc_font_size' ) );
	echo '<input type="text" name="tka_lsc_font_size" value="'.$font_size.'" placeholder="10px" />';
}
function tka_css_font_weight(){
	$font_color = esc_attr( get_option( 'tka_lsc_font_weight' ) );
	echo '<input type="text" name="tka_lsc_font_weight" value="'.$font_color.'" placeholder="900" />';
}

function tka_css_font_color(){
	$font_color = esc_attr( get_option( 'tka_lsc_font_color' ) );
	echo '<input type="text" name="tka_lsc_font_color" value="'.$font_color.'" placeholder="#000000" />';
}
// fonction field general
function tka_general_title(){
	$title = esc_attr( get_option( 'tka_lsc_title' ) );
	echo '<input type="text" name="tka_lsc_title" value="'.$title.'" placeholder="Prefix" /><p><i>by default : "<b>Empty</b>"</i></p>';
}
function tka_general_format(){
	$format = esc_attr( get_option( 'tka_lsc_format' ) );
	$html='<select name="tka_lsc_format">';

	if ($format==12) {
		$html.='<option value="24" >Half system (24 hours)</option>';
		$html.='<option value="12" selected="selected">Full system (12 hours)</option>';	
	}else{
		$html.='<option value="24" selected="selected">Half system (24 hours)</option>';
		$html.='<option value="12">Full system (12 hours)</option>';
	}

	$html.='</select>';
	echo $html;

}
function tka_general_fuseau(){
	$fuseau = esc_attr( get_option( 'tka_lsc_fuseau' ) );
	$temps = array(+12,+11,+10,+9,+8,+7,+6,+5,+4,+3,+2,+1,0,-1,-2,-3,-4,-5,-6,-7,-8,-9,-10,-11,-12);
	$html = '<select name="tka_lsc_fuseau"><option value="">Default</option>';
	$selected='';
	$gmt='';

	foreach ($temps as $key => $value) {
		if($fuseau===esc_attr($value)){
			$selected="selected='selected'";
		}else{
			$selected='';
		}
		if($value==0){
			$gmt=' (GMT)';
		}else{
			$gmt='';
		}
		$html .='<option value="'.$value.'" '.$selected.'>'.$value.$gmt.'</option>';
	}

	$html .='</select><p><i>by default : "<b>0 (GMT)</b>"</i></p>';
	echo $html;
}

function tka_general_hidesecond(){
	$hidesecond = esc_attr( get_option( 'tka_lsc_hidesecond' ) );
	$html = '' ;
	if($hidesecond == true){
		$html .= '<input type="checkbox" name="tka_lsc_hidesecond" value="true" checked />';
	}else{
		$html .= '<input type="checkbox" name="tka_lsc_hidesecond" value="false" />';
	}

	$html .='<p><i>by default : "<b>Visible</b>"</i></p>';
	echo $html;
}

function tka_simple_clock_admin_callback(){
	require ( plugin_dir_path( __FILE__ ) . 'template/tka-simple-clock-admin-field.php');
}



