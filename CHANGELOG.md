### Unreleased

* Add `display_value` to all fields and use this for rendering in the display-mode
  templates.
* Provide helper method on FormElementRenderer to provide the criteria-based highlight classes
* Include highlight_if and hide_display_if in the field element models
  (we need access to them for display-mode code)
* Refactor config to allow easy use and overriding of defaults
* Refactor rendering to render templates directly
* First version, extracted from host project
