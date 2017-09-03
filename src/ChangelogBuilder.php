<?php
namespace Changelog;

/**
 * @internal
 */
class ChangelogBuilder
{
    /** @var boolean */
    private $verbose;

    /** @var string */
    private $releaseNotesOutputDir;

    /** @var string */
    private $baseDir;

    /** @var boolean */
    private $newServiceFlag = false;

    const CHANGELOG_FEATURE = 'feature';
    const CHANGELOG_API_CHANGE = 'api-change';
    const CHANGELOG_ENHANCEMENT = 'enhancement';
    const CHANGELOG_BUGFIX = 'bugfix';

    /**
     *  The constructor requires following configure parameters:
     * - base_dir: (String) Path to the base directory where the `.changes` folder is located. Default is empty string.
     * - release_notes_output_dir: (String) Path to `.changes` folder where new release json will be put and content of
     *                              `nextrelease` folder will be deleted
     * - verbose: (Boolean) Flag to enable(true)/disable(false) verbose mode
     */
    public function __construct(array $params)
    {
        $this->baseDir = isset($params['base_dir']) ? $params['base_dir'] : '';
        $this->releaseNotesOutputDir = isset($params['release_notes_output_dir'])
            ? $params['release_notes_output_dir']
            : '';
        $this->verbose = isset($params['verbose']) ? $params['verbose'] : false;
    }

    public function isNewService()
    {
        return $this->newServiceFlag;
    }

    private function readChangelog()
    {
        $releaseDir = $this->baseDir . '.changes/nextrelease/';
        $changelogEntries = [];
        if (!is_dir($releaseDir) || !$dh = opendir($releaseDir)) {
            throw new \InvalidArgumentException(
                "nextrelease directory doesn't exists or is not readable at location $releaseDir"
            );
        }
        //Ignore any files starting with a (.) dot
        $files = preg_grep('/^([^.])/', scandir($releaseDir));
        if (empty($files)) {
            throw new \InvalidArgumentException("No release notes files found in $releaseDir folder");
        }
        foreach ($files as $file) {
            $str = file_get_contents($releaseDir . $file);
            $changelogEntries = array_merge($changelogEntries, $this->cleanJSON(json_decode($str)));
        }
        closedir($dh);

        $this->newServiceFlag = count(array_filter($changelogEntries, function ($change) {
                return $change->type === self::CHANGELOG_FEATURE;
            })) > 0;

        return $changelogEntries;
    }

    private function cleanJSON($arr)
    {
        if (empty($arr) || !is_array($arr)) {
            throw new \RuntimeException('Invalid Input', 2);
        }

        return $arr;
    }

    private function setTimezone($default)
    {
        $timezone = "";
        
        // On many systems (Mac, for instance) "/etc/localtime" is a symlink
        // to the file with the timezone info
        if (is_link("/etc/localtime")) {
            
            // If it is, that file's name is actually the "Olsen" format timezone
            $filename = readlink("/etc/localtime");
            
            $pos = strpos($filename, "zoneinfo");
            if ($pos) {
                // When it is, it's in the "/usr/share/zoneinfo/" folder
                $timezone = substr($filename, $pos + strlen("zoneinfo/"));
            } else {
                // If not, bail
                $timezone = $default;
            }
        }
        else {
            // On other systems, like Ubuntu, there's file with the Olsen time
            // right inside it.
            $timezone = file_get_contents("/etc/timezone");
            if (!strlen($timezone)) {
                $timezone = $default;
            }
        }
        date_default_timezone_set($timezone);
    }

    private function createChangelogFile($changelogFile){
        self::setTimezone('America/Los_Angeles');
        $content = "# CHANGELOG\n\n## 0.0.0 - " 
        . date("m-d-y") 
        . "\n\n* `` - Add `ChangelogBuilder` to the repository \n\n";
        $fp = fopen($changelogFile,"wb");
        fwrite($fp,$content);
        fclose($fp);
    }

    private function createTag($changelogFile)
    {
        if (!file_exists($changelogFile)) {
            self::createChangelogFile($changelogFile);
        }
        $lines = file($changelogFile);
        $tag = explode(".", explode(" ", $lines[2])[1]);
        if ($tag[0] == 'next') {
            throw new \InvalidArgumentException('Untagged changes exits in CHANGELOG.md', 1);
        }
        if ($this->newServiceFlag) {
            //Minor Version Bump if a newservice is being released
            ++$tag[1];
            $tag[2] = 0;
            return implode(".", $tag);
        } else {
            ++$tag[2];
            return implode(".", $tag);
        }
    }

    private function createChangelogJson($changelog, $tag)
    {
        $fp = fopen($this->releaseNotesOutputDir . ".changes/" . $tag, 'w');
        fwrite($fp, json_encode($changelog, JSON_PRETTY_PRINT));
        fclose($fp);
    }

    private function writeToChangelog($changelog, $changelogFile)
    {
        if (!file_exists($changelogFile)) {
            throw new \InvalidArgumentException('Changelog File Not Found', 2);
        }
        $newChangeLog = "## next release\n\n" . $changelog . "\n";
        $lines = file($changelogFile);
        $lines[2] = $newChangeLog . $lines[2];
        file_put_contents($changelogFile, $lines);
    }

    public function cleanNextReleaseFolder()
    {
        $nextReleaseDir = $this->baseDir . '.changes/nextrelease/';
        $files = preg_grep('/^([^.])/', scandir($nextReleaseDir));
        foreach ($files as $file) {
            if (is_file($nextReleaseDir . $file)) {
                unlink($nextReleaseDir . $file);
            }
        }
    }

    private function generateChangelogString($changelog)
    {
        usort($changelog, function ($a, $b) {
            return strcmp($a->category, $b->category);
        });
        $str = "";
        foreach ($changelog as $log) {
            $str .= "* `" . $log->category . "` - " . $log->description . "\n";
        }
        return $str;
    }

    public function buildChangelog()
    {
        $changelogFile = $this->baseDir . 'CHANGELOG.md';
        $newChangelog = $this->readChangelog();
        $tag = $this->createTag($changelogFile);
        putenv("TAG=$tag");
        if ($this->verbose) {
            echo 'Tag for next release ' . $tag . "\n";
        }
        $this->createChangelogJson($newChangelog, $tag);
        $ChangelogUpdate = $this->generateChangelogString($newChangelog);
        if ($this->verbose) {
            echo "$ChangelogUpdate";
        }
        $this->writeToChangelog($ChangelogUpdate, $this->releaseNotesOutputDir . 'CHANGELOG.md');
    }
}