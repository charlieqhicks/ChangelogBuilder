<?php

require __DIR__ . '/../src/ChangelogBuilder.php';

use Changelog\ChangelogBuilder;

$params = [];

$options = getopt('v');

$params['verbose'] = isset($option['v']) ? $option['v'] : true;

$changelogBuilder = new ChangelogBuilder($params);

## Build the Changelog File
$changelogBuilder->buildChangelog();

## Clean the nextrelease folder with applied changlog blurbs
$changelogBuilder->cleanNextReleaseFolder();
