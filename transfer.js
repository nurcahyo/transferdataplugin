(function(){

var $alert, $placement, $adminBar, xhr

$alert = jQuery('<div id="update-bizgym-alert" class="alert alert-warning fade in" role="alert" style="z-index:9999999;position:fixed;top:0;width:100%">\
      <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>\
      <p>We invite you to visit the new and improved BizGym 2.0 to claim your new free starting profile.  \
      Click <a id="update-bizgym-link" href="#!"> here </a> to continue \
      </p>\
    </div>')


$placement = jQuery(document.body)

$alert.appendTo($placement)
$alert = jQuery('#update-bizgym-alert')
$alert.on('click', '#update-bizgym-link', function(e) {
	e.preventDefault()
	if (typeof xhr == 'function') xhr.abort()
	xhr = jQuery.ajax({
		url: '/bg-upgrade',
		success: function(resp) {
			alert(resp.message)
			$alert.alert('close')
		}
	})

})
setTimeout(function() {
	$adminBar = jQuery('#wpadminbar')
	if ($adminBar.length > 0) {	
		$alert.css('top', $adminBar.height())
	}
}, 4000)


}).call(this)
