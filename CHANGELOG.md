### Unreleased

## v0.2.0 (2018-03-13)

* Switch to using the inGenerator kohana fork and updated kohana-extras

## v0.1.0 (2018-02-26)

* Add ShowgroupHelper
* Add all form classes to the dependency container with FormDependencyFactory
* Add FormEditRenderer and FormDisplayRenderer interfaces : currently these are 
  both implemented by FormElementRenderer, but depend on them instead where possible
  to avoid a potential future breaking change to rendering.
* Add `display_value` to all fields and use this for rendering in the display-mode
  templates.
* Provide helper method on FormElementRenderer to provide the criteria-based highlight classes
* Include highlight_if and hide_display_if in the field element models
  (we need access to them for display-mode code)
* Refactor config to allow easy use and overriding of defaults
* Refactor rendering to render templates directly
* First version, extracted from host project
