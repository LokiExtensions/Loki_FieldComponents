# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.2.41] - 12 June 2026
### Fixed
- Replace LokiComponentFormKey with Alpine.store form key

## [2.2.40] - 01 June 2026
### Fixed
- Fix issue with `text_select` field type not finding ViewModel properly
- Allowing overriding field attributes via XML layout
- Throw friendly exception when field.phtml is called without ViewModel
- Add CSS class `field-type-X`
- Double-check $viewModel is set to prevent possible error

## [2.2.39] - 21 May 2026
### Fixed
- Allow for better UI of help-toggling

## [2.2.38] - 15 May 2026
### Fixed
- Add `name` attribute to input and select fields

## [2.2.37] - 11 May 2026
### Fixed
- Fix issue with original select-options loosing their value

## [2.2.36] - 07 May 2026
### Fixed
- Add support for optgroups within a select-field

## [2.2.35] - 05 May 2026
### Fixed
- Move field-completed block to div again, because template does not work without proper HTML
- Change isCompleted-box into an Alpine template to remove HTML clutter

## [2.2.34] - 29 April 2026
### Fixed
- Fix Tailwind 4 flex-col

## [2.2.33] - 22 April 2026
### Fixed
- Add field-type comboxbox (experimental)
- Base radio-box its ID upon field-ID, not template-ID

## [2.2.32] - 08 April 2026
### Fixed
- Make sure JSON HTML attributes are accompanied with single quotes
- Allow for HTML attribute via XML layout to be non-string (like array)

## [2.2.31] - 17 March 2026
### Fixed
- Do not set `flex` CSS class on loader when it is hidden (fixing Breeze issue)
- Do not set fields to disabled when loading, because of UI flow breaking

## [2.2.30] - 12 March 2026
### Fixed
- Allow for date-input tuning via `min_date` and `max_date`

## [2.2.29] - 12 March 2026
### Fixed
- Skip separate label for checkboxes and radiobuttons

## [2.2.28] - 02 March 2026
### Fixed
- Set `requiredErrorMessage` prop from block arg `required_error_message`
- Allow for component to override `requiredErrorMessage`

## [2.2.27] - 19 February 2026
### Fixed
- Field Components are not required by default
- Rename `onComponentValidate()` into `validate()` method

## [2.2.26] - 18 February 2026
### Fixed
- Set skipValidation = false for any component by default

## [2.2.25] - 16 February 2026
### Fixed
- Issue when streetname contained spaces
- Make sure aria-invalid is set if any of the local messages contain errors

## [2.2.24] - 13 February 2026
### Fixed
- Add new Alpine.js flag for required to make validation simpler

## [2.2.23] - 12 February 2026
### Fixed
- Allow PHP Config to read from current store level
- Add Option-model for select-fields (with disabled flag)
- Remove autocomplete when field is disabled

## [2.2.22] - 03 February 2026
### Fixed
- Set aria-invalid to root element if it contains role=group
- Allow configuring field_name via XML layout

## [2.2.21] - 28 January 2026
### Fixed
- Rename deprecated class import
- Rename componentValidate into onComponentValidate
- Properly pass component to helper function
- Allow for custom componentValidate() function in Loki Components JS

## [2.2.20] - 23 January 2026
### Fixed
- Wrap select in additional div to allow for proper ID

## [2.2.19] - 20 January 2026
### Fixed
- Trigger loki-components.component.update event as soon as valid changes

## [2.2.18] - 12 January 2026
### Fixed
- Do not add additional ID to root node of button
- Copy generic CI/CD files

## [2.2.17] - 19 December 2025
### Fixed
- Add skipValidation flag for read-only components

## [2.2.16] - 17 December 2025
### Fixed
- Add $isRequiredByDefault to fields

## [2.2.15] - 12 December 2025
### Fixed
- Allow `default_value` via XML layout

## [2.2.14] - 11 December 2025
### Fixed
- Set value in HTML to avoid Alpine rendering hickup

## [2.2.13] - 02 December 2025
### Fixed
- Better styling of help popup

## [2.2.12] - 21 November 2025
### Fixed
- Replace `$block->getChildHtml()` with `$childRenderer->all()` including better sorting

## [2.2.11] - 12 November 2025
### Fixed
- Fix JS error when object is empty
- Add plan-trial keyword to composer file
- Update composer keywords
- Update composer keywords

## [2.2.10] - 22 October 2025
### Fixed
- Do not escape `$css()` with `escapeHtmlAttr()` but `escapeHtml()`

## [2.2.9] - 17 October 2025
### Fixed
- Fix merge conflict
- Document latest version of template
- Copy generic CI/CD files
- Update MODULE.json

## [2.2.8] - 15 October 2025
### Fixed
- Bump

## [2.2.7] - 15 October 2025
### Fixed
- Cleanup image templates with width, height and size

## [2.2.6] - 10 October 2025
### Fixed
- Set aria-disabled=true on step-forward-button if not valid
- Add button hint to step-forward button
- Add role=tooltip to help popup

## [2.2.5] - 08 October 2025
### Fixed
- Fix password field styling under Luma

## [2.2.4] - 08 October 2025
### Fixed
- Use flexbox to align content in button
- Always enable the step-forward button, just change its appearance

## [2.2.4] - 08 October 2025
### Fixed
- Use flexbox to align content in button
- Always enable the step-forward button, just change its appearance

## [2.2.3] - 07 October 2025
### Fixed
- Add appearance:none to input switch

## [2.2.2] - 06 October 2025
### Fixed
- Always add autocomplete=off to field by default
- Throw exception when template renderer is not found in critical templates

## [2.2.1] - 30 September 2025
### Fixed
- Add simple overlay template without loader icon
- Update README

## [2.2.0] - 23 September 2025
### Fixed
- Remove redundant CSS classes from icon containers
- Wrong HTML ID for radioboxes
- Implement new imageRenderer
- Add vertical align to button
- Better CSS classes in button and buttons

## [2.1.5] - 16 September 2025
### Fixed
- Rename loki-components.alpinejs to loki.alpinejs
- Manual merging of `LokiCheckout_PostcodeNl` MR #11

## [2.1.4] - 04 September 2025
### Fixed
- Rename FieldTemplateLocator to FieldTypeManager
- Allow configuring fieldTypes via DI XML
- Rename form/field/text.phtml to form/field/input.phtml
- Remove obsolete FieldContext
- Make FieldViewModel no longer abstract

## [2.1.3] - 03 September 2025
### Fixed
- Move LokiFieldViewModelImageOutput to LokiComponentsUtilImageOutput to remove circular dependency

## [2.1.2] - 02 September 2025
### Fixed
- Fix CI

## [2.1.1] - 02 September 2025
### Fixed
- Fix PHP 8.1 issue in unit test

## [2.1.0] - 02 September 2025
### Added
- Add accessibility value to hidden labels
- Add labels to buttons
- Allow adding field attributes via XML layout
- Refactor hard-coded field attributes to FieldViewModel::getFieldAttributes()

### Fixed
- Remove LengthBehaviourInterface
- Remove hasMin / hasMax / getMin / getMax methods

## [2.0.16] - 28 August 2025
### Fixed
- Add CI files
- Replace yireo/opensearch with yireo/opensearch-dummy in Gitlab CI
- Remove obsolete autofill hack

## [2.0.15] - 26 August 2025
### Fixed
- Allow field label to come from XML layout
- Add support for Alpine Mask config via XML layout
- Fix Alpine Mask plugin not being loaded under Hyva
- Rename Alpine CSP template; Load Alpine Mask under Luma properly
- Disable autocomplete for checkboxes

## [2.0.14] - 21 August 2025
### Fixed
- Add CSS class `cursor-pointer` to entire label of radio field
- Replace LokiComponentsUtilBlockCssClass with LokiCssUtilsUtilCssClass
- Declare used PHP namespaces
- Document latest version of template
- Add missing `strict_types` declaration

## [2.0.13] - 18 August 2025
### Fixed
- Lower requirements to PHP 8.1
- Fix newlines after comments
- Add aria-hidden to asterisk
- Replace `autocomplete=1` with `autocomplete=on`
- Fix non-CSP-compliant `x-show` statement
- Remove duplicate LokiToggle component
- Add label to switch
- Finalize improved switch field with only input and label element
- Fixes for radio buttons
- Make switch field-type CSP compliant and visual improvements
- Rename tab in Store Config from "Yireo" to "Loki"

## [2.0.12] - 15 August 2025
### Fixed
- Fix JS warning on value.length if null

## [2.0.11] - 15 August 2025
### Fixed
- Mark input field with [data-valid] also on page load when it is valid and not-empty

## [2.0.10] - 15 August 2025
### Fixed
- Do not show loader-overlay on load by adding x-cloak

## [2.0.9] - 15 August 2025
### Fixed
- Add inline styling (for CSS properties) via `$style()`
- Move config from Yireo tab to Loki tab

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
