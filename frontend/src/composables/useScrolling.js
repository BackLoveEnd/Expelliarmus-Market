import { nextTick } from 'vue'

export function useScrolling () {
  function scrollToTop () {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  function scrollToView (elementId) {
    nextTick(() => {
      const element = document.getElementById(elementId)
      if (element) {
        element.scrollIntoView({ behavior: 'smooth' })
      }
    })
  }

  return { scrollToTop, scrollToView }
}
