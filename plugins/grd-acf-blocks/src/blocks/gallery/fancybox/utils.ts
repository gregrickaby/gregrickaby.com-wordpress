import {Slide} from './types'

/**
 * Disable right-click on main image.
 *
 * A user can still use the download
 * button to get a better quality JPG.
 */
export function disableRightClick(): void {
  document.addEventListener(
    'contextmenu',
    (event) => {
      if (
        event.target instanceof HTMLElement &&
        event.target.nodeName === 'IMG'
      ) {
        event.preventDefault()
      }
    },
    false
  )
}

/**
 * Customize the download button.
 *
 * Replace WebP URL with JPG URL.
 * Remove target="_blank" attribute.
 * Fix download attribute.
 *
 * @return {boolean|void} False if no download button exists.
 */
export function customizeDownloadButton(): boolean | void {
  // Get the download button.
  const downloadButton = document.querySelector('a[download]')

  // Check if the download button exists.
  if (!downloadButton || !downloadButton.getAttribute('href')) {
    return false
  }

  // Get the image URL
  const url = downloadButton.getAttribute('href') || ''

  // Replace the WebP URL with the JPG URL.
  downloadButton.setAttribute('href', url.replace(/-jpe?g.webp/, '.jpg'))

  // Remove the target attribute.
  downloadButton.removeAttribute('target')

  // Add the download attribute.
  downloadButton.setAttribute('download', '')
}
