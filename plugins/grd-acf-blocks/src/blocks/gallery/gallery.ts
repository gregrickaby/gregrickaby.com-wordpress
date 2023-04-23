import {Fancybox} from '@fancyapps/ui'
import Masonry from 'masonry-layout'
import imagesLoaded from 'imagesloaded'
import '@fancyapps/ui/dist/fancybox.css'
import {disableRightClick} from './lib/utils'
import './gallery.css'

// Wait for the DOM to be ready.
document.addEventListener('DOMContentLoaded', () => {
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

  // Get the gallery grid.
  const gallery = document.querySelector('.grd-acf-block-grid')

  // If there is no gallery, return.
  if (!gallery) {
    return
  }

  /**
   * Init Masonry.
   *
   * @see https://masonry.desandro.com/options.html
   */
  const masonryGrid = new Masonry(gallery, {
    itemSelector: '.grd-acf-block-image',
    columnWidth: '.grd-acf-block-grid-sizer',
    percentPosition: true
  })

  /**
   * Init imagesLoaded.
   *
   * @see https://imagesloaded.desandro.com/
   */
  const imagesLoadedInstance = imagesLoaded(gallery)

  // When images have all loaded, init the Masonry layout.
  imagesLoadedInstance.on('done', () => {
    masonryGrid.layout()
  })
})
