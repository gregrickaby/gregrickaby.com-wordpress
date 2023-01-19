import {Fancybox} from '@fancyapps/ui'
import '@fancyapps/ui/dist/fancybox.css'
import {Slide} from './types'
import {
  customizeCaption,
  customizeDownloadButton,
  disableRightClick
} from './utils'

/**
 * Initialize Fancybox.
 *
 * Bind to the core gallery block's links.
 *
 * @see https://fancyapps.com/docs/ui/fancybox/events *
 */
Fancybox.bind('.wp-block-gallery a', {
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
  caption: (slide: Slide) => {
    customizeCaption(slide)
  },
  on: {
    '*': () => {
      customizeDownloadButton()
    },
    load: () => {
      disableRightClick()
    }
  }
})
