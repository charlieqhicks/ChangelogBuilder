help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "  clean          to delete all Makefile artifacts"
	@echo "  clear-cache    to clear the cached JSON compiled SDK files"
	@echo "  test           to perform unit tests.  Provide TEST to perform a specific test."
	@echo "  coverage       to perform unit tests with code coverage. Provide TEST to perform a specific test."
	@echo "  coverage-show  to show the code coverage report"
	@echo "  integ          to run integration tests. Provide TEST to perform a specific test."
	@echo "  guide          to build the user guide documentation"
	@echo "  guide-show     to view the user guide"
	@echo "  api            to build the API documentation. Provide ISSUE_LOGGING_ENABLED to save build issues to file."
	@echo "  api-show       to view the API documentation"
	@echo "  api-package    to build the API documentation as a ZIP"
	@echo "  api-manifest   to build an API manifest JSON file for the SDK"
	@echo "  compile-json   to compile the JSON data files in src/data into PHP files"
	@echo "  package        to package a phar and zip file for a release"
	@echo "  check-tag      to ensure that the TAG argument was passed"
	@echo "  tag            to chag tag a release based on the changelog. Must provide a TAG"
	@echo "  release        to package the release and push it to GitHub. Must provide a TAG"
	@echo "  full-release   to tag, package, and release the SDK. Provide TAG"

test:
	vendor/bin/phpunit tests

