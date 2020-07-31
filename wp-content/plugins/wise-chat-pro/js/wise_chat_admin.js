/**
 * Wise Chat admin support JS
 *
 * @author Kainex <contact@kaine.pl>
 * @link https://kaine.pl/projects/wp-plugins/wise-chat-pro
 */
jQuery(document).ready(function($){
	jQuery('.wc-color-picker').wpColorPicker();
	jQuery('.wc-image-picker').click(function(e) {
		e.preventDefault();
		var button = jQuery(this);
		var targetId = button.data('target-id');
		var imageContainerId = button.data('image-container-id');
		var target = jQuery('#' + targetId);
		var imageContainer = jQuery('#' + imageContainerId);
		var frame = wp.media({
			title: 'Select or Upload Emoticon Image',
			button: {
				text: 'Use this image'
			},
			multiple: false
		});

		frame.on('select', function() {
			var attachment = frame.state().get('selection').first().toJSON();

			target.val(attachment.id);
			imageContainer.html('<img src="' + attachment.url + '" style="max-width: 100px;" />');
		});

		frame.open();
	});

	jQuery('.new-emoticon-submit').click(function(e) {
		var attachmentId = jQuery('#newEmoticonId').val();
		var alias = jQuery('#newEmoticonAlias').val();
		if (attachmentId.length == 0) {
			e.preventDefault();
			alert('Please select the image first.');
			return;
		}
		var href = jQuery(this).attr('href') + '&newEmoticonAttachmentId=' + encodeURIComponent(attachmentId) + '&newEmoticonAlias=' + encodeURIComponent(alias) + '&tab=emoticons';
		jQuery(this).attr('href', href);
	});

	jQuery('.new-pm-rule-submit').click(function(e) {
		var source = jQuery('[name="newPmRuleSource"]').val();
		var target = jQuery('[name="newPmRuleTarget"]').val();

		var href = jQuery(this).attr('href') + '&newPmRuleSource=' + encodeURIComponent(source) + '&newPmRuleTarget=' + encodeURIComponent(target) + '&tab=permissions';
		jQuery(this).attr('href', href);
	});

	jQuery("form input[type='checkbox']").change(function(event) {
		var target = jQuery(event.target);
		var childrenSelector = "*[data-parent-field='" + target.attr('id') + "']";
		if (target.is(':checked')) {
			jQuery(childrenSelector).attr('disabled', null);
		} else {
			jQuery(childrenSelector).attr('disabled', '1');
		}
	});

	var childrenSelector = "*[data-parent-field='custom_emoticons_enabled']";
	if (jQuery("input#custom_emoticons_enabled").is(':checked')) {
		jQuery(childrenSelector).attr('disabled', null);
	} else {
		jQuery(childrenSelector).attr('disabled', '1');
	}

	function addCheckboxesBind(parentCheckbox, childrenCheckboxesName, selectCheckboxesName) {
		jQuery(parentCheckbox).change(function(event) {
			if (!this.checked) {
				return;
			}

			var areAccessRolesSelected = false;
			jQuery(childrenCheckboxesName).each(function () {
				if (this.checked) {
					areAccessRolesSelected = true;
				}
			});

			if (areAccessRolesSelected === false) {
				jQuery(selectCheckboxesName).each(function () {
					jQuery(this).prop('checked', true);
				});
			}
		});
	}

	addCheckboxesBind(
		'#access_mode', "input[name='wise_chat_options_name[access_roles][]'", "input[name='wise_chat_options_name[access_roles][]'"
	);

	jQuery('.wc-save-notification-button').click(function(e) {
		var form = jQuery(this).closest('.wc-notification-form');
		var action = form.find('#notificationAction').val();
		var frequency = form.find('#notificationFrequency').val();
		var recipientEmail = form.find('#notificationRecipientEmail').val();
		var subject = form.find('#notificationSubject').val();
		var content = form.find('#notificationContent').val();

		if (recipientEmail.length == 0) {
			alert('Recipient\'s e-mail is required.');
			e.preventDefault();
		} else if (subject.length == 0) {
			alert('Subject is required.');
			e.preventDefault();
		} else if (content.length == 0) {
			alert('Content is required.');
			e.preventDefault();
		} else {
			var href = jQuery(this).attr('href') +
				'&action=' + encodeURIComponent(action) +
				'&frequency=' + encodeURIComponent(frequency) +
				'&recipientEmail=' + encodeURIComponent(recipientEmail) +
				'&subject=' + encodeURIComponent(subject) +
				'&content=' + encodeURIComponent(content) +
				'&tab=notifications';
			jQuery(this).attr('href', href);
		}
	});

	jQuery('.wc-save-user-notification-button').click(function(e) {
		var form = jQuery(this).closest('.wc-user-notification-form');
		var frequency = form.find('#userNotificationFrequency').val();
		var subject = form.find('#userNotificationSubject').val();
		var content = form.find('#userNotificationContent').val();

		if (subject.length == 0) {
			alert('Subject is required.');
			e.preventDefault();
		} else if (content.length == 0) {
			alert('Content is required.');
			e.preventDefault();
		} else {
			var href = jQuery(this).attr('href') +
				'&frequency=' + encodeURIComponent(frequency) +
				'&subject=' + encodeURIComponent(subject) +
				'&content=' + encodeURIComponent(content) +
				'&tab=notifications';
			jQuery(this).attr('href', href);
		}
	});

});