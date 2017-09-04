# Changelog Builder

[![Build Status](https://img.shields.io/travis/imshashank/ChangelogBuilder.svg?style=flat)](https://travis-ci.org/imshashank/ChangelogBuilder)
[![codecov](https://codecov.io/gh/imshashank/ChangelogBuilder/branch/master/graph/badge.svg)](https://codecov.io/gh/imshashank/ChangelogBuilder)
[![Latest Stable Version](https://poser.pugx.org/imshashank/changelog-builder/version)](https://packagist.org/packages/imshashank/changelog-builder)
[![Total Downloads](https://poser.pugx.org/imshashank/changelog-builder/downloads)](https://packagist.org/packages/imshashank/changelog-builder)
[![License](https://poser.pugx.org/imshashank/changelog-builder/license)](https://packagist.org/packages/imshashank/changelog-builder)

## Ideology

We believe each PR must be accompanied by an entry to the CHANGELOG.md. An up-to-date changelog helps in tracking which changes were merged in a given release. It also gives contributers their fair share and allows them to track in which release were their changes merged to the repository.

The Changelog Builder automatically processes all changelog entries. Each pull request is required to have a changelog JSON blob as part of the request. The system also calculates the next version for the package based on the type of the changes that are defined in the given changelog JSON blob.

The update simplifies the process of adding release notes to the CHANGELOG.md file for each pull request. Each merged pull request that was part of the release results in a new entry to the CHANGELOG.md file. The entry describes the change and provides the TAG number and release date.

## Dependencies

`chag` is used to update the changelog version in CHANGELOG.md

Install chag using

```
curl -s https://raw.githubusercontent.com/mtdowling/chag/master/install.sh | bash

```
        

## Installing using composer

Add the following to your composer file and then run `composer install`

```
{
        "imshashank/changelog-builder": "*"
}
```

## Using Changelog

Create a new blurb in folder `.changes/nextrelease/`. The name of the json blrb should be unique in that folder.
To create a new Changelog, just run the below command.

```
<?php

require __DIR__ . '/../src/ChangelogBuilder.php';

use Changelog\ChangelogBuilder;

date_default_timezone_set('America/Los_Angeles');

$params = [];

$options = getopt('v');

$params['verbose'] = isset($option['v']) ? $option['v'] : true;

$params['prefix'] = 'ChangelogBuilder';

$changelogBuilder = new ChangelogBuilder($params);

## Build the Changelog File
$tag = $changelogBuilder->buildChangelog();

## Tags the git repository with the generated tag
shell_exec('chag update '. $tag);

## Cleans the nextrelease folder
$changelogBuilder->cleanNextReleaseFolder();
```

## Running Tests

Run the below command to run tests.

```
make test
```
The following is a sample changelog blob.

    [
        {
            "type"       : "feature|enhancement|bugfix",
            "category"   : "Target of Update",
            "description": "English language simple description of your update."
        }
    ]

Each changelog blob is required to define the “type”, “category”, and “description” fields. The “category” explains the service that the change is associated with. For release changes that are not related to any service, the category field is left as an empty string. The “description” field should contain one or two sentences detailing the changes.

The “type” field describes the scope of the change being proposed. This field helps the Changelog Builder decide if a minor version bump is needed. The “type” field is assigned one of the following values:

- feature: A major change to the release that will cause a minor version bump. A feature will open a new use case or significantly improve -upon an existing one. The update will result in a minor version bump. Example: a new service.
- enhancement: A small update to the code. This should not cause any code to break and should only enhance given functionality of the release. The update will result in a patch version update. Example: Documentation Update.
- bugfix: A fix in the code that has caused some unwanted behavior. The update will result in a patch version update.
The changelog blob that will be included in the next release must be put inside the .changes/nextrelease folder. The changelog blob file name must be unique in that folder. A good practice is to give a descriptive name to the changelog blob file related to the request.

On each release, the builder looks inside the .changes/nextrelease folder and consolidates all JSON blobs into a single JSON document. Then the builder calculates the next version number, based on the full JSON document.

**Example:**

Current release Version: 1.2.3
Changelog Blob File 1: update-client.json

    [
        {
            "type"       : "enhancement",
            "category"   : "tests",
            "description": "Adds support for new feature in test"
        }
    ]

**Changelog Blob File 2:** documentation-update.json

    [
        {
            "type"       : "enhancement",
            "category"   : "Client",
            "description": "Update documentation for operation foo bar in Client"
        }
    ]

**Changelog Blob File 3:** repo-bugfix.json

    [
        {
            "type"       : "bugfix",
            "category"   : "",
            "description": "Fixes a typo in Client"
        }
    ]

**Consolidated JSON document:** .changes/1.2.4.json
Next release version: 1.2.4

    [
      {
        "type": "enhancement",
        "category": "tests",
        "description": "Adds support for new feature in test"
      },
      {
        "type": "enhancement",
        "category": "Client",
        "description": "Update documentation for operation foo bar in Client"
      },
      {
        "type": "bugfix",
        "category": "",
        "description": "Fixes a typo in Client"
      }
    ]

A new file, with the name VERSION_NUMBER.json, will be created in the .changes folder with the contents of the JSON blob for the changelog. You can see the previous changelog JSON blobs here. If needed, we can reconstruct the CHANGELOG.md file using the JSON documents in the .changes folder.

On a successful release, the changelog entries are written to the top of the CHANGELOG.md file. Then chag is used to tag the release and label the added release notes with the current version number.

# Future Work
- Add command `make change` to trigget Changelog Builder for next release
- Add spelling & format checker to `ChangelogBuilder`
