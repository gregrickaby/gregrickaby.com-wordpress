/**
 * Disable right-click on main image.
 *
 * Why? Because we're displaying a lower-quality
 * WebP in the lightbox. If a user right-clicks
 * and saves the WebP, they're OS may not support
 * viewing it offlint. Additionally, if a user
 * tries to upload the WebP to a print service,
 * I've learned that big-box stores don't support it.
 *
 * Alternatively, the user can click the download button
 * to get the full-quality JPG.
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
