### Unreleased

## v1.2.1 (2021-04-20)

* Support PHP8

## v1.2.0 (2020-11-02)

* Support php 7.4

## v1.1.0 (2020-05-14)

* Support kohana-extras 1.0 or 2.0 (the BC breaks don't affect us)

## v1.0.0 (2019-04-03)

* Drop support for php5

## v0.2.7 (2019-03-08)

* Fix all risky tests by adding sensible assertions

## v0.2.6 (2019-02-18)

* Support customisable placeholder text on the `choice-or-other` field (defaults to the 
  previously-hardcoded 'Please state')
* Support the `add_empty_choice` option on choice-or-other fields (defaults to true for BC)

## v0.2.5 (2019-01-08)

* Support choice-radio fields

## v0.2.4 (2018-12-06)

* Support kohana-extras 0.4

## v0.2.3 (2018-09-04)

* Switch to namespaced PHPUnit classes

## v0.2.2 (2018-03-20)

* Allow render of entire form in display mode

## v0.2.1 (2018-03-20)

* Fix reference to ShowgroupHelper in field templates

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
