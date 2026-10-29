# Loki_FieldComponents

<!-- badges.specs.start -->
<div class="github-specs-badges">

![Magento version](https://img.shields.io/badge/Magento-2.4.6%20%7C%202.4.9-orange)
![PHP version](https://img.shields.io/badge/PHP-8.2%E2%80%938.5-777BB4)
![License](https://img.shields.io/badge/License-OSL--3.0-blue)
![Latest Version](https://img.shields.io/packagist/v/loki/magento2-field-components)

</div>
<!-- badges.specs.end -->


**This Magento 2 module contains Loki Field Components, as is being used by the Loki Checkout. Loki Field Components are Loki Components built for one specific purpose: To have the value of one specific HTML form field be saved to the Magento 2 backend via the repository of your choice. Thanks to this package, adding a new field to the Loki Checkout requires about 15 lines of code.**

## Installation
Install this package via composer:
```bash
composer require loki/magento2-field-components
```

Next, enable this module:
```bash
bin/magento module:enable Loki_FieldComponents
```

## Current status

<!-- badges.test.start -->
<div class="github-pipeline-badges">

![Static Tests](https://img.shields.io/github/actions/workflow/status/LokiExtensions/Loki_FieldComponents/static-tests.yml?label=static-tests)
![Unit Tests](https://img.shields.io/github/actions/workflow/status/LokiExtensions/Loki_FieldComponents/unit-tests.yml?label=unit-tests)
![Integration Tests](https://img.shields.io/github/actions/workflow/status/LokiExtensions/Loki_FieldComponents/integration-tests.yml?label=integration-tests)
![Playwright](https://img.shields.io/github/actions/workflow/status/LokiExtensions/Loki_FieldComponents/playwright.yml?label=playwright)
![DI Compilation](https://img.shields.io/github/actions/workflow/status/LokiExtensions/Loki_FieldComponents/compile.yml?label=compile)

</div>
<!-- badges.test.end -->
