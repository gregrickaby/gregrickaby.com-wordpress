import {Fancybox} from '@fancyapps/ui'
import '@fancyapps/ui/dist/fancybox/fancybox.css'
import imagesLoaded from 'imagesloaded'
import Masonry from 'masonry-layout'
import './gallery.scss'

/**
 * Wait for the the initial HTML document to be completely loaded.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/API/Document/DOMContentLoaded_event
 */
document.addEventListener('DOMContentLoaded', () => {
  // Get the ACF Block Gallery grid.
  const gallery = document.querySelector('.grd-photo-gallery-grid')

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
    gutter: 24,
    itemSelector: '.grd-photo-gallery-image',
    columnWidth: '.grd-photo-gallery-grid-sizer',
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
    defaultDisplay: 'flex',
    Toolbar: {
      display: {
        left: ['infobar'],
        middle: [
          'zoomIn',
          'zoomOut',
          'toggle1to1',
          'rotateCCW',
          'rotateCW',
          'flipX',
          'flipY'
        ],
        right: ['slideshow', 'thumbs', 'download', 'close']
      }
    }
  })
})
