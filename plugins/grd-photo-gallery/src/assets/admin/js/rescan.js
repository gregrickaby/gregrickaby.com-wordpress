jQuery(document).ready(function ($) {
  $(document).on('click', '#rescan-metadata', function (e) {
    e.preventDefault()
    const attachment_id = $(this).data('attachment-id')
    const spinner = $('.spinner')
    spinner.css('visibility', 'visible')

    const data = {
      action: 'rescan_metadata',
      nonce: ajax_object.nonce,
      attachment_id
    }

    $.post(ajax_object.ajax_url, data).always(function () {
      spinner.css('visibility', 'hidden')
      location.reload()
    })
  })
})
