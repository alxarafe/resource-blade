#!/bin/bash
# Description: Runs the PHPUnit test suite.

echo "Running PHPUnit Tests..."
docker exec alxarafe-resources ./vendor/bin/phpunit
