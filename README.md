# Loki_FieldComponents

<!-- badges.specs.start -->
![Magento version](https://img.shields.io/badge/Magento-2.4.6%20%7C%202.4.9-orange)
![PHP version](https://img.shields.io/badge/PHP-8.2%E2%80%938.5-777BB4)
![License](https://img.shields.io/badge/License-OSL--3.0-blue)
![Latest Version](https://gitlab.yireo.com/loki-checkout/Loki_FieldComponents/-/badges/release.svg)
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
[![Static Tests](https://gitlab.yireo.com/loki-checkout/Loki_FieldComponents/badges/main/pipeline.svg?job=static-tests)](https://gitlab.yireo.com/loki-checkout/Loki_FieldComponents/-/pipelines)
[![Unit Tests](https://gitlab.yireo.com/loki-checkout/Loki_FieldComponents/badges/main/pipeline.svg?job=unit-tests)](https://gitlab.yireo.com/loki-checkout/Loki_FieldComponents/-/pipelines)
[![Integration Tests](https://gitlab.yireo.com/loki-checkout/Loki_FieldComponents/badges/main/pipeline.svg?job=integration-tests)](https://gitlab.yireo.com/loki-checkout/Loki_FieldComponents/-/pipelines)
[![Playwright](https://gitlab.yireo.com/loki-checkout/Loki_FieldComponents/badges/main/pipeline.svg?job=playwright)](https://gitlab.yireo.com/loki-checkout/Loki_FieldComponents/-/pipelines)
[![DI Compilation](https://gitlab.yireo.com/loki-checkout/Loki_FieldComponents/badges/main/pipeline.svg?job=compile)](https://gitlab.yireo.com/loki-checkout/Loki_FieldComponents/-/pipelines)
<!-- badges.test.end -->
