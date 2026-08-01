# Project Constitution

## Core Governance
1. **Backward Compatibility:**
   - Existing Theme Mods (`logo_light`, `logo_light_2x`, `logo_dark`, `logo_dark_2x`, `gnn_accent_color`, `gnn_default_theme`, `gnn_footer_legal`) MUST never be removed or broken.
2. **Panel Control Principle:**
   - Every theme feature, effect, badge, topbar, app dock, and preloader MUST be controllable (toggleable on/off and customizable) via GNN Theme Panel.
3. **No Hardcoded Content:**
   - Templates MUST NOT contain hardcoded brand strings or untranslatable texts.
4. **Performance & Light Weight:**
   - Maintain zero external admin framework policy (Pure WP Settings API).
   - Bundle self-hosted WOFF2 fonts locally.
5. **Code Style & WordPress Standards:**
   - Follow WordPress Coding Standards (PHPCS WP rules).
   - Use strict comparisons (`===`, `!==`) and type checking.
