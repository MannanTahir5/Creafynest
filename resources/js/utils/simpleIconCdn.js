/**
 * Simple Icons SVGs on jsDelivr. Avoid `simple-icons@v13/icons/...` — that path is an
 * incomplete mirror; the package root resolves to current icons and matches slug names.
 */
export const SIMPLE_ICONS_CDN_BASE = 'https://cdn.jsdelivr.net/npm/simple-icons/icons'

export function simpleIconSvgUrl(slug) {
  if (slug == null || slug === '') {
    return ''
  }
  return `${SIMPLE_ICONS_CDN_BASE}/${String(slug).trim()}.svg`
}
