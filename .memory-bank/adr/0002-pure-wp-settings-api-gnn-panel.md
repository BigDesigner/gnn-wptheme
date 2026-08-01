# 2. Pure WordPress Settings API for GNN Theme Panel

- **Status:** Accepted
- **Confidence:** Verified
- **Date:** 2026-08-01

## Context
A dedicated theme administration panel is required to allow non-technical site owners to toggle and configure every theme feature, animation, logo height, and layout option without editing code.

## Decision
Build the GNN Theme Panel strictly using native WordPress Settings API (`register_setting()`, `add_menu_page()`, `settings_fields()`) without third-party options frameworks (such as Redux, Codestar, or Kirki).

## Consequences
- Zero external framework overhead and security risk.
- Fast admin load times.
- Native WP Admin UI appearance and security compliance.
