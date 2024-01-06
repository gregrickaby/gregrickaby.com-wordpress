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
      .success(function () {
        messageBox.text('Success! Metadata has been rescanned.')
        location.reload()
      })
      .fail(function () {
        messageBox
          .text('Error! Unable to rescan metadata.')
          .show()
          .delay(3000)
          .fadeOut()
      })
      .always(function () {
        spinner.css('visibility', 'hidden')
      })
  })
})
