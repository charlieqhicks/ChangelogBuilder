# Changelog Builder

[![Build Status](https://img.shields.io/travis/aws/aws-sdk-php.svg?style=flat)](https://travis-ci.org/imshashank/ChangelogBuilder)
[![codecov](https://codecov.io/gh/imshashank/ChangelogBuilder/branch/master/graph/badge.svg)](https://codecov.io/gh/imshashank/ChangelogBuilder)

The Changelog Builder automatically processes all changelog entries. Each pull request is required to have a changelog JSON blob as part of the request. The system also calculates the next version for the package based on the type of the changes that are defined in the given changelog JSON blob.

The update simplifies the process of adding release notes to the CHANGELOG.md file for each pull request. Each merged pull request that was part of the release results in a new entry to the CHANGELOG.md file. The entry describes the change and provides the TAG number and release date.

The following is a sample changelog blob.

    [
        {
            "type"       : "feature|enhancement|bugfix",
            "category"   : "Target of Update",
            "description": "English language simple description of your update."
        }
    ]

Each changelog blob is required to define the “type”, “category”, and “description” fields. The “category” explains the service that the change is associated with. For SDK changes that are not related to any service, the category field is left as an empty string. The “description” field should contain one or two sentences detailing the changes.

The “type” field describes the scope of the change being proposed. This field helps the Changelog Builder decide if a minor version bump is needed. The “type” field is assigned one of the following values:

- feature: A major change to the SDK that will cause a minor version bump. A feature will open a new use case or significantly improve -upon an existing one. The update will result in a minor version bump. Example: a new service.
- enhancement: A small update to the code. This should not cause any code to break and should only enhance given functionality of the SDK. The update will result in a patch version update. Example: Documentation Update.
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
