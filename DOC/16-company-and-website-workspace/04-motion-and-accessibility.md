# 04 — Motion & Accessibility

## Experience goal

Create a premium, smooth feeling through useful transitions, clear interaction feedback, and meaningful details—not by animating every element.

## Motion system

- Use restrained entrances, hover/focus feedback, section transitions, and controls that clarify hierarchy or state.
- Apply consistent durations and easing.
- Prefer transform and opacity for routine effects.
- Keep navigation, CTAs, forms, and primary content usable without waiting for animation.
- Avoid scroll hijacking, stacked parallax, animated cursors, autoplay media, and heavy 3D that adds cost without helping users.
- Do not add a large animation framework unless implementation needs justify it.

## Accessibility

- Respect reduced-motion preferences.
- Support keyboard navigation and visible focus.
- Use semantic elements, logical headings, labels, and useful validation errors.
- Maintain sufficient contrast in both themes.
- Do not convey status only through color.
- Give meaningful images useful alt text and keep decorative imagery out of the reading flow.
- Test RTL/LTR, zoom, touch targets, keyboard sequence, and smaller screens.

## Light/dark themes

Design backgrounds, surfaces, text, borders, forms, overlays, focus and status colors for both themes. Use system preference initially and provide a clear user toggle. Persist an explicit choice and avoid a visible flash at page load. Switching theme must not reset page/form state.

## Acceptance checks

The site stays usable with animation disabled; no mobile horizontal overflow; focus order is predictable; critical content is immediately available; controls and status text are readable in both themes.
