<?php

function tka_live_simple_clock_shortcode()
{
	//css

	$font = esc_attr(get_option('tka_lsc_font'));
	$font_size = esc_attr(get_option('tka_lsc_font_size'));
	$font_weight = esc_attr(get_option('tka_lsc_font_weight'));
	$font_color = esc_attr(get_option('tka_lsc_font_color'));

	$codeCSS = '';
	if ($font != '') {
		$codeCSS .= 'font-family:' . $font . ';';
	}
	if ($font_size != '') {
		$codeCSS .= 'font-size:' . $font_size . ';';
	}
	if ($font_weight != '') {
		$codeCSS .= 'font-weight:' . $font_weight . ';';
	}
	if ($font_color != '') {
		$codeCSS .= 'color:' . $font_color . ';';
	}
	//end css

	$format = esc_attr(get_option('tka_lsc_format'));
	$fuseau = esc_attr(get_option('tka_lsc_fuseau'));
	$title = esc_attr(get_option('tka_lsc_title'));
	$hidesecond = esc_attr(get_option('tka_lsc_hidesecond'));

	if ($fuseau == '') {
		$fuseau = '0';
	}
	if ($format == 12) {
		$temp = 'ampm = h >= 12 ? "pm" : "am";h=h % 12;h=h ? h : 12;';
	} else {
		$temp = '';
	}

	$clock_html = '<span id="tka_time" class="tka_style" style="' . $codeCSS . '"></span>';

	$clock_script = '
<script>
	function checkTime(i) {
  if (i < 10) {
    i = "0" + i;
  }
  return i;
}

function startTime() {
  var ampm="";
  var today = new Date();

  var n = today.getTimezoneOffset();
  var temp=(' . $fuseau . '*60)/60;
  var h = today.getHours();
  	h=(temp+h);' . $temp . '
  
  var m = today.getMinutes();
  var s = today.getSeconds();
  // add a zero in front of numbers<10
  m = checkTime(m);
  s = checkTime(s);';

	if ($hidesecond == true) {
		$clock_script .= ' document.getElementById("tka_time").innerHTML ="' . $title . ' "+ h + ":" + m +" "+ampm;';
	} else {
		$clock_script .= ' document.getElementById("tka_time").innerHTML ="' . $title . ' "+ h + ":" + m + ":" + s +" "+ampm;';
	}



	$clock_script .= '
  t = setTimeout(function() {
    startTime()
  }, 500);
}


startTime();

</script>';
	return $clock_html . $clock_script;
}
add_shortcode('live_simple_clock', 'tka_live_simple_clock_shortcode');
