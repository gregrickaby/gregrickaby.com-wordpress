import {Fancybox} from '@fancyapps/ui'
import '@fancyapps/ui/dist/fancybox.css'
import {disableRightClick} from './utils'

/**
 * Initialize Fancybox.
 *
 * Bind to the core gallery block's links.
 *
 * @see https://fancyapps.com/docs/ui/fancybox/events
 */
Fancybox.bind('[data-fancybox="gallery"]', {
  groupAll: true, // Group all items
  Toolbar: {
    display: [
      {id: 'prev', position: 'center'},
      {id: 'counter', position: 'center'},
      {id: 'next', position: 'center'},
      'zoom',
      'slideshow',
      'fullscreen',
      'download',
      'thumbs',
      'close'
    ]
  },
  on: {
    load: () => {
      disableRightClick()
    }
  }
})
