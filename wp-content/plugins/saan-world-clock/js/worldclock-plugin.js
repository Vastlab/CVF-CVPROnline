jQuery( document ).ready(function() {
	moment.locale(jQuery(".wc_time").data("wc-language"));
	var deviceTime, serverTime, actualTime, timeOffset;
		
	function updateDisplay(){
		jQuery(".wc_time").each(function(index, obj){
			if(/\d/.test(jQuery(this).data("wcTimezone"))){
				jQuery(this).text(moment().utcOffset(jQuery(this).data("wcTimezone")).format(jQuery(this).data("wcFormat")));
			}
			else{
				jQuery(this).text(actualTime.tz(jQuery(this).data("wcTimezone")).format(jQuery(this).data("wcFormat")));
			}
		});
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
			serverTime = moment(new Date(dateHeader));
			timeOffset = serverTime.diff(moment());
			timerHandler();
		}
		xmlhttp.open("HEAD", window.location.href);
		xmlhttp.send();
	}
	fetchServerTime();
});