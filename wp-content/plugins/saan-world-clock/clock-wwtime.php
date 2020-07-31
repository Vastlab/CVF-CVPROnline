<?php
/*****************************************************************************************

 * Plugin Name: Saan World Clock
 * Plugin URI: https://zeitin.de/plugin
 * Description: World Clock Plugin - Current Time Worldwide - with Shortcodes
 * Author: andy kramer from saan.digital
 * Author URI: https://saan.digital/
 * Version: 1.4
 * Update Server: https://zeitin.de/plugin/download/Saan-WorldClock.zip
 
 
******************************************************************************************/
// [w_clock tz='Zurich/Europe' f='LL LTS']

function world_clock_shortcode( $atts ) {
	wp_register_script('moment-locales', plugin_dir_url( __FILE__ ) . '/js/moment-with-locales.min.js', array('jquery'));
	wp_enqueue_script('moment-timezone-data', plugin_dir_url( __FILE__ ) . '/js/moment-timezone-with-data.min.js', array('moment-locales'));
	wp_enqueue_script('worldclock-plugin', plugin_dir_url( __FILE__ ) . '/js/worldclock-plugin.js',array('moment-timezone-data'));
    $a = shortcode_atts( array(
        'tz' => 'timezone',
        'f' => 'format',
    ), $atts );
	if(!$a['tz'] == ""){
    	return '<span class="wc_time" data-wc-format="'.$a['f'].'" data-wc-timezone="'.$a['tz'].'" data-wc-language="'.get_locale().'"></span>';
	}
	else{
		return '<span class="wc_time" >wrong Shortcode!</span>';
	}
}
function wc_get_shortcode_option_page() {
	$wc_current_time_zone = get_option('timezone_string');
	$wc_f_hours = array('/^25/','/^75/','/^5/');
	$wc_minutes = array(':15',':45',':30');
	if(empty($wc_current_time_zone)){
		$wc_current_time_zone = get_option('gmt_offset');
		$wc_utc = explode(".", $wc_current_time_zone);
		$wc_utc_min = preg_replace($wc_f_hours, $wc_minutes, $wc_utc[1]);
		if($wc_utc[0] > 0){
			$wc_before_utc = "+";
		}
		$wc_current_time_zone = "UTC ".$wc_before_utc.$wc_utc[0].$wc_utc_min;
	}
	wp_register_script('moment-locales', plugin_dir_url( __FILE__ ) . '/js/moment-with-locales.min.js', array('jquery'));
	wp_enqueue_script('moment-timezone-data', plugin_dir_url( __FILE__ ) . '/js/moment-timezone-with-data.min.js', array('moment-locales'));
	?>
	<!-- Options in Admin -->
	<div class="wrap">
		<h2>World Clock</h2>
		<p>Your currently set time zone at Wordpress: <strong><?= $wc_current_time_zone ?>
			(<span id="local_time"></span>)</strong> <a href="options-general.php">change</a>
		</p>
	</div>
	<h3>Generate Shortcode</h3>
	<table class="form-table">
		<tr>
			<th>Choose time zone</th>
			<td>
				<select id="wc_selected_timezone">
					<?php if (function_exists('wp_timezone_choice')){
						echo wp_timezone_choice($wc_current_time_zone);
					}?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Choose the format</th>
			<td>
				<select id="wc_selected_format">
					<option value="LTS">Time</option>
					<option value="LL">Date</option>
					<option value="LL LTS">Daten and Time</option>
				</select>
			</td>	
		</tr>
		<tr>
			<th>Preview</th>
			<td><p id="chose_time"></p></td>
		</tr>
	</table>
	<div class="wrap">
		<h3>Shortcode</h3>
		<p>Copy this shortcode and paste it to your desired place.</p>
		<input type="text" id="wc_shortcode_for_timezone" style="width:400px;">
		<input type="button" value="Copy to Clipboard" id="wc_copy_button" class="button-primary">
		<input type="hidden" value="" id="wc_offset_chose">
	</div>
	<p>
		&nbsp;
	</p>
	<hr>
	<p>presented by</p>
	<a href="https://zeitin.de" target="_blank" rel="noopener">
		<img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/zeitin-de-logo.png'; ?>" style="width:100%;max-width:350px;">
	</a>
	<p>If you enjoy using <strong>Saan World Clock</strong> for WordPress, please leave us a <a href="https://wordpress.org/support/view/plugin-reviews/saan-world-clock?rate=5#postform" target="_blank">★★★★★</a> rating. A huge thank you in advance!</p>
	
	<script  type='text/javascript'>
	jQuery( document ).ready(function() {
		jQuery("#wc_selected_timezone, #wc_selected_format").change(function () {
			var chose_timezone = jQuery("#wc_selected_timezone option:selected").val();
			if(!chose_timezone.search(/UTC/i)){
				chose_timezone = jQuery("#wc_selected_timezone option:selected").text()
				chose_timezone = chose_timezone.replace(/UTC/i, "");
				chose_timezone = chose_timezone.replace(/(^|\D)(\d)(?!\d)/g, '$10$2');
				jQuery("#wc_offset_chose").val( chose_timezone );
			}
			else{
				jQuery("#wc_offset_chose").val("");
			}
			jQuery("#wc_shortcode_for_timezone").val("[w_clock tz='" + jQuery("#wc_selected_timezone option:selected").val() + "' f='" + jQuery("#wc_selected_format option:selected").val() + "']");
	  }).change();
		jQuery('#wc_copy_button').on("click", function(){
        	var value = jQuery('#wc_shortcode_for_timezone').val();
 			var $temp = jQuery("<input>");
			jQuery("body").append($temp);
			$temp.val(value).select();
			document.execCommand("copy");
			$temp.remove();
    	});
	//live time / preview ------------------------------------------------------------
		moment.locale('<?php echo get_locale(); ?>');
		var deviceTime,
			serverTime,
			actualTime,
			timeOffset;
		function updateDisplay(){
			jQuery("#local_time").text(actualTime.tz('<?= $wc_current_time_zone ?>').format('LL - LTS'));
			if(jQuery("#wc_offset_chose").val() == ""){
				jQuery("#chose_time").text(actualTime.tz(jQuery("#wc_selected_timezone option:selected").val()).format(jQuery("#wc_selected_format option:selected").val()));
			}
			else{
		jQuery("#chose_time").text(moment().utcOffset(jQuery("#wc_offset_chose").val()).format(jQuery("#wc_selected_format option:selected").val()));
			}
		}
		function timerHandler(){
			actualTime = moment();
			actualTime.add(timeOffset);
			updateDisplay();
			setTimeout(timerHandler, (1000 - (new Date().getTime() % 1000)));
		}
		function fetchServerTime(){
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onload = function() {
				var dateHeader = xmlhttp.getResponseHeader('Date');
				deviceTime = moment();
				serverTime = moment(new Date(dateHeader)); // Read
				timeOffset = serverTime.diff(moment());
				timerHandler();
			}
			xmlhttp.open("HEAD", window.location.href);
			xmlhttp.send();
		}
		fetchServerTime();
	});
	</script>
	<?php
}//End wc_get_shortcode_option_page()

// Admin menu
function wc_shortcode_option_add_menu() {
  add_options_page('World Clock - Time world wide', 'World Clock', 9, __FILE__, 'wc_get_shortcode_option_page');
}

//WordPress-Hooks
add_action('admin_menu', 'wc_shortcode_option_add_menu');
add_shortcode('w_clock', 'world_clock_shortcode');
?>