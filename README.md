# Status - WIP

Work in progress but not much is expected.

# Azure Cognitive Services/Search integration for Silverstripe

This is a simple module created for Bing Custom Search that now runs with Azure Cognitive Services/Search.

## Requirements
- silverstripe/cms ^4.0 || ^5.0

## Installation
[Composer](https://getcomposer.org/) is the recommended method for installing Silverstripe modules.

```
require lerni/bing-custom-search
```

## How to use
Create a Bing Custom Search Config (https://www.customsearch.ai/) to set up a config and get an Access Key (https://portal.azure.com/#create/Microsoft.BingCustomSearch). You can set these in the yml-config or via the .env file (AZURE_COGNITIVE_SEARCH_KEY, AZURE_COGNITIVE_SEARCH_CUSTOMCONFIG). Search results are meant to be loaded via [htmX](https://htmx.org/)/Ajax and therefore HTML is returned. `data-hx-` Form attributes are set, so you just need to load htmx.js in template.
