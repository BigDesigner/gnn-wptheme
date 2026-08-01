# 3. Custom Post Type Based Hero Slider

- **Status:** Accepted
- **Confidence:** Verified
- **Date:** 2026-08-01

## Context
The front-page hero slider needs to support unlimited slides, rich content per slide (kicker, title, text, buttons, image positioning), and straightforward WordPress Admin management.

## Decision
Implement a Custom Post Type `gnn_slide` with custom meta boxes for slide parameters, registered in `gnn/inc/slider.php`.

## Consequences
- Unlimited slides can be created, ordered, and edited like standard WordPress posts.
- Full support for full-height slider option (`slider_full_height`) and custom JS autoplay controls.
