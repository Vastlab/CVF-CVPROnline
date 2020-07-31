/* function ect_create_newwindow(event){
    var ect_href = event.currentTarget.getAttribute('href')
 window.open(ect_href, "", "width=800,height=400");
}
 */

jQuery(document).ready(function ($) {
  $('a.ect-share-link').click(function(e){
    var ect_href = event.currentTarget.getAttribute('href')
    window.open(ect_href, "", "width=800,height=400");
  });
});