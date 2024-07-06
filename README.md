# Addresses
[![License](https://poser.pugx.org/t3brightside/addresses/license)](LICENSE.txt)
[![Packagist](https://img.shields.io/packagist/v/t3brightside/addresses.svg?style=flat)](https://packagist.org/packages/t3brightside/addresses)
[![Downloads](https://poser.pugx.org/t3brightside/addresses/downloads)](https://packagist.org/packages/t3brightside/addresses)
[![Brightside](https://img.shields.io/badge/by-t3brightside.com-orange.svg?style=flat)](https://t3brightside.com)

**TYPO3 CMS extension for address lists.**

**[Demo](https://microtemplate.t3brightside.com/)**

## Features
- List of addresses from pages or selected records
- Image crop
- Sort by
- Basic category filter in BE
- Pagination with items per page and unique to the content element with [paginatedprocessors](https://github.com/t3brightside/paginatedprocessors)
- Social links with icons
- Easy to add custom templates
- Bidirectional connection to pages
- Contact to ext:[personnel](https://extensions.typo3.org/extension/personnel/) records using ext:[addressespersonnel](https://extensions.typo3.org/extension/addressespersonnel/)

## System requirements
- TYPO3
- fluid_styled_content
- paginatedprocessors
- embedassets

## Installation
- `composer req t3brightside/addresses` or from TYPO3 extension repository **[addresses](https://extensions.typo3.org/extension/addresses/)**
- Add static template
- Include static template for Paginatedprocessors
- Change extension configuration for enabling features like: show BE thumbnails, allow connection in page properties, disable pagination

## Usage
- Create address records in a Page/Sysfolder
- Add desired content element and point to the Page/Sysfolder or individual records

### Add custom template
**TypoScript**
Check the constant editor.

**PageTS**
```
TCEFORM.tt_content.tx_addresses_template.addItems {
  minilist = Mini List
}
```

**Fluid**
Add new section wheres IF condition determines template name 'minilist' to: _Resources/Private/Templates/Addresses.html_
```xml
<f:if condition="{data.tx_addresses_template} == minilist">
  <f:for each="{addresses}" as="address" iteration="iterator">
    <f:render partial="Minilist" arguments="{_all}"/>
  </f:for>
</f:if>
```
Create new partial: _Resources/Private/Partials/Minilist.html_

### routeEnhancers
For the pagination routing check [t3brightside/paginatedprocessors](https://github.com/t3brightside/paginatedprocessors#readme)

## Development & maintenance
[Brightside OÜ – TYPO3 development and hosting specialised web agency](https://t3brightside.com/)
