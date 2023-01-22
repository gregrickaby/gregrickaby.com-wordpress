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
