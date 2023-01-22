import {Fancybox} from '@fancyapps/ui'
import '@fancyapps/ui/dist/fancybox.css'
import {disableRightClick} from './lib/utils'
import './gallery.css'

/**
 * Init Fancybox.
 *
 * @see https://fancyapps.com/docs/ui/fancybox/options
 * @see https://fancyapps.com/docs/ui/fancybox/plugins/toolbar
 */
Fancybox.bind('[data-fancybox]', {
  animated: false,
  groupAll: true,
  infinite: false,
  preload: 3,
  Toolbar: {
    display: ['zoom', 'slideshow', 'fullscreen', 'download', 'thumbs', 'close']
  },
  on: {
    load: () => {
      disableRightClick()
    }
  }
})
