import {Fancybox} from '@fancyapps/ui'
import Masonry from 'masonry-layout'
import imagesLoaded from 'imagesloaded'
import '@fancyapps/ui/dist/fancybox.css'
import {disableRightClick} from './lib/utils'
import './gallery.scss'

/**
 * Wait for the the initial HTML document to be completely loaded.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/API/Document/DOMContentLoaded_event
 */
document.addEventListener('DOMContentLoaded', () => {
  // Get the ACF Block Gallery grid.
  const gallery = document.querySelector('.grd-acf-block-grid')

  // If there is no gallery, return.
  if (!gallery) {
    return
  }

  /**
   * Init imagesLoaded.
   *
   * @see https://imagesloaded.desandro.com/
   */
  const imagesLoadedInstance = imagesLoaded(gallery)

  /**
   * Init Masonry.
   *
   * @see https://masonry.desandro.com/options.html
   */
  const masonryGrid = new Masonry(gallery, {
    itemSelector: '.grd-acf-block__image',
    columnWidth: '.grd-acf-block-grid__sizer',
    percentPosition: true
  })

  // When images have all loaded, init Masonry layout.
  imagesLoadedInstance.on('done', () => {
    masonryGrid.layout()
  })

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
      display: [
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
})
