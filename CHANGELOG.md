# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.1] - 13 May 2025
### Fixed
- Update MODULE.json
- Update license
- Rewrite Alpine from initActions object to methods starting with init
- Do not re-enable an initially disabled field after reload
- Add PHTML attribute disabled again anyway
- Allow PHP 8.4 in CI
- Update composer and module deps

## [1.0.0] - 25 April 2025
### Fixed
- Remove x-model because of CSP compliance
- Allow for upgrade to LokiComponents 1.0
- Draft of fast checkout option for PSPs
- Fix `this.field` into `this.$refs.field`
- Move all field behaviour to FieldComponentType
- Remove gridClass, add colSpan and mdColSpan in grid layouts
- Remove FieldTemplateLocator from context
- Rewrite FieldTemplateLocator usage to be independent of context
- Make loader icon in fields depend on Alpine and activate only after configurable timeout
- Add comment option to fields
- Fix Alpine overriding disabled state in select

## [0.0.5] - 16 April 2025
### Fixed
- Add JS password complexity validation
- Move scripts from "before.body.end" to new "loki-scripts" container
- Add configurable password complexity
- Enhance look&feel
- Rename LokiCheckoutValidator to LokiComponentValidator
- Password visibility, password repeat

## [0.0.4] - 08 April 2025
### Fixed
- Remove select-icon when loading select-field
- Add textarea field-sizing-content
- Mobile fixes

## [0.0.3] - 04 April 2025
### Fixed
- Remove specific step button templates in favor of FieldComponents
- Modify overlay when loading next step
- Add icons to step button
- Disable field via Alpine instead
- Add ID per form field
- Add help text properly
- Fetch help text from ViewModel instead of block

## [0.0.2] - 11 March 2025
### Fixed
- Rename `field_name` to fieldName
- Fix client-side validation after refactoring to LokiFieldComponents
- Add input prefix
- Register Tailwind and module properly
- Add Alpine x-mask
- Add new input types
- Add switch in basic form
- Fetch input label from XML layout by default
- Fix location of buttons
- Move required block
- Add module dependencies

## [0.0.1] - 27 February 2025
- Initial release
