# Azure Cognitive Services/Search integration for Silverstripe

Simple module created for Bing Custom Search and now runs with Azure Cognitive Services/Search.

## Requirements
- silverstripe/cms ^4.0 || ^5.0

## Installation
[Composer](https://getcomposer.org/) is the recommended method for installing Silverstripe modules.

```
require lerni/bing-custom-search
```

## How to use
Create an Bing Custom Search Config (https://www.customsearch.ai/) to setup a config & get an Access-Key (https://portal.azure.com/#create/Microsoft.BingCustomSearch). You can set those in yml-config or per .env file (AZURE_COGNITIVE_SEARCH_KEY, AZURE_COGNITIVE_SEARCH_CUSTOMCONFIG). Search results are meant to be loaded via [htmX](https://htmx.org/)/Ajax and therefor it returns html (hypermedia). `hx-` Form-attributes are set, that you just need to load htmx.js in your template.
