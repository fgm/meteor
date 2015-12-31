This module exists to support the Meteor fgm:drupal-sso package: neither is of
any use without the other one.

See more information on https://atmospherejs.com/fgm/drupal-sso


Configuration
-------------

- In order for the site to work, the cookie domain must allow cookie sharing
  between Drupal and Meteor. Since Drupal defaults to using the site name as
  cookie domain, this means using an explicit cookie domain in
  `sites/(site)/services.yml` like this:

```yaml
    parameters:
      session.storage.options:
        #
        # <...snip...>
        #
        # Drupal automatically generates a unique session cookie name based on the
        # full domain name used to access the site. This mechanism is sufficient
        # for most use-cases, including multi-site deployments. However, if it is
        # desired that a session can be reused across different subdomains, the
        # cookie domain needs to be set to the shared base domain. Doing so assures
        # that users remain logged in as they cross between various subdomains.
        # To maximize compatibility and normalize the behavior across user agents,
        # the cookie domain should start with a dot.
        #
        # @default none
        cookie_domain: '.acme.dev'
        #
```

- Do not forget the leading dot on the cookie domain, as in the above example
- Per the cookie specification, use at least a domain two levels deep as in the
  above example (`.foo.dev`), not just one level deep (`.dev`) as this would
  break cookie sharing.
- MacOS X: do __not__ use a `.local` subdomain, as this triggers Bonjour name
  lookups, usually causing 5 seconds delays on HTTP operations initiated by the
  Meteor application. Using `local` as a private TLD is invalid anyway, and
  this is a known problem since at least Snow Leopard, still present on El
  Capitan. Any other TLD (like `.dev`) works just fine.
