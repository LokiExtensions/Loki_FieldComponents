# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.8] - 13 August 2025
### Fixed
- Stretch div of buttons in full width
- Add `data-valid` attribute to fields
- Add `isValid` method
- Always set `submitted=true` after component update
- Allow property `required` to be properly set from XML layout

## [2.0.7] - 10 August 2025
### Fixed
- Add escaping to templates
- Allow toggling loader overlay via showLoader property
- Rewrite loader CSS to allow displaying either inline SVG or imager
- Make sure help-icon and loader do not overlap
- Configure loader via XML layout to be either inline SVG or image

## [2.0.6] - 06 August 2025
### Fixed
- Move initMethods and destroyMethods into component partials
- Implement aria-errormessage
- Fix empty fields when component value is null
- Render valid=false if required and empty
- Rewrite aria-invalid params from PHP to Alpine
- Add submitted flag to all components
- Lower PHP requirement to PHP 8.2+

## [2.0.5] - 01 August 2025
### Fixed
- Add missing `$css` wrapper
- Prevent removed component from triggering warnings on missing $refs.field

## [2.0.4] - 30 July 2025
### Fixed
- Add ARIA labels

## [2.0.3] - 25 July 2025
### Fixed
- Fix SVG image output bug with better debugging

## [2.0.2] - 24 July 2025
### Fixed
- Fix issue with empty icon output

## [2.0.1] - 22 July 2025
### Fixed
- If event value and component value, do not submit anything
- Remove debugging output
- Upgrade CI tests to Magento 2.4.8 and PHP 8.4

## [2.0.0] - 21 July 2025
### Fixed
- Make sure name in layout is always string
- Rename PHP namespace from `Yireo_Loki*` to `Loki*`
- Rename composer package from `yireo/magento2-loki*` to `loki/magento2*`

## [1.0.5] - 16 July 2025
### Fixed
- Add block `loki-components.utils.field-completed` for icons within input fields

## [1.0.4] - 08 July 2025
### Fixed
- Allow configuring of help icon via XML layout

## [1.0.3] - 26 May 2025
### Fixed
- Add new method to get image by URL

## [1.0.2] - 22 May 2025
### Fixed
- Make width of help configurable
- Add setValid method to Alpine components
- Update admin settings with tooltip and regenerate new MODULE.json

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
