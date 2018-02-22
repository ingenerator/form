A schema-based form rendering and interaction library

[![Build Status](https://travis-ci.org/ingenerator/form.svg?branch=0.1.x)](https://travis-ci.org/ingenerator/form)

## Schema-based?

This library allows you to define and interact with forms using a simple array / JSON schema - perfect
for allowing forms that are customisable by end-users. That said, you can also just as easily use them
for forms that are created from code - with a few limitations. It also supports rendering a 
form in display mode, and optionally defining fields that should be hidden / highlighted for display
based on their value and/or whether the user completed them.

Basic validation is implemented, but broadly only by applying the same HTML5 constraints that are
tagged up to be enforced by well-behaved browsers. So you don't need to re-implement that, but 
anything more complex (value == confirm_value, email DNS checks etc) needs to be manually implemented
in the code that uses the form.  


# Installing kohana-form

This isn't in packagist yet : you'll need to add our package repository to your composer.json:

```json
{
  "repositories": [
    {"type": "composer", "url": "https://php-packages.ingenerator.com"}
  ]
}
```

`$> composer require ingenerator/form`

# Contributing

Contributions are welcome but please contact us before you start work on anything to check your
plans line up with our thinking and future roadmap. 

# Contributors

This package has been sponsored by [inGenerator Ltd](http://www.ingenerator.com)

* Andrew Coulton [acoulton](https://github.com/acoulton) - Lead developer

# Licence

Licensed under the [BSD-3-Clause Licence](LICENSE)
