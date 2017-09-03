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

shell_exec('chag update '. $tag);

## Clean the nextrelease folder with applied changlog blurbs
$changelogBuilder->cleanNextReleaseFolder();
