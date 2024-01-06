jQuery(document).ready(function ($) {
  $(document).on('click', '#rescan-metadata', function (e) {
    e.preventDefault()

    const spinner = $('.spinner')
    const messageBox = $('#rescan-message')

    spinner.css('visibility', 'visible')

    const data = {
      action: 'rescan_metadata',
      nonce: ajax_object.nonce,
      attachment_id: ajax_object.attachment_id
    }

    $.post(ajax_object.ajax_url, data)
      .done(function (response) {
        if (!response.success) {
          messageBox.text('Error! Metadata could not be rescanned.')
        } else {
          messageBox.text('Success! Metadata has been rescanned.')
        }
        // Show the message for 3 seconds, then fade out.
        messageBox.show().delay(3000).fadeOut()
      })
      .fail(function () {
        messageBox
          .text('Error! Unable to process request.')
          .show()
          .delay(3000)
          .fadeOut()
      })
      .always(function () {
        spinner.css('visibility', 'hidden')
      })
  })
})
