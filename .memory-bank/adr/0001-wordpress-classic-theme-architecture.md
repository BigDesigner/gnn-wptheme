# 1. WordPress Classic Theme Architecture

- **Status:** Accepted
- **Confidence:** Verified
- **Date:** 2026-08-01

## Context
The GNN theme is designed as a high-performance, dark-mode corporate IT / cybersecurity WordPress theme compatible with standard WordPress block editor, Elementor Free, and WooCommerce.

## Decision
Adopt a classic PHP-template architecture with modular helper files under `gnn/inc/`, native Gutenberg Block Patterns under `gnn/inc/pattern-parts/`, and self-hosted WOFF2 variable fonts.

## Consequences
- High performance without external JS framework bloat.
- Full compatibility with WordPress 6.0+ core template hierarchy.
- Seamless Elementor canvas and full-width layout support.
