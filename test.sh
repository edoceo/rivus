#!/bin/bash
#
# Rivus Test Runner
#

set -o errexit
set -o errtrace
set -o nounset
set -o pipefail

x=${RIVUS_TEST_ORIGIN:-}
if [ -z "$x" ]
then
        echo "You have to define the environment first"
        exit 1
fi

f=$(readlink -f "$0")
d=$(dirname "$f")

cd "$d"

rm -fr ./webroot/test-output/

declare -rx OUTPUT_BASE="webroot/test-output"
declare -rx OUTPUT_MAIN="${OUTPUT_BASE}/index.html"
declare -rx SOURCE_LIST="boot.php lib/ sbin/ test/"

mkdir -p "${OUTPUT_BASE}"


#
# Lint
# bash -x test/phplint.sh


#
# PHP-CPD
# bash -x test/phpcpd.sh

#
# PHPStan
# bash -x test/phpstan.sh


#
# PHPUnit
xsl_file="test/phpunit.xsl"

vendor/bin/phpunit \
        --configuration "test/phpunit.xml" \
        --log-junit "$OUTPUT_BASE/phpunit.xml" \
        --testdox-html "$OUTPUT_BASE/testdox.html" \
        --testdox-text "$OUTPUT_BASE/testdox.txt" \
        --testdox-xml "$OUTPUT_BASE/testdox.xml" \
        "$@" 2>&1 | tee "$OUTPUT_BASE/phpunit.txt"


#[ -f "$xsl_file" ] || curl -qs 'https://openthc.com/pub/phpunit/report.xsl' > "$xsl_file"
#
#xsltproc \
#        --nomkdir \
#        --output "$OUTPUT_BASE/phpunit.html" \
#        "$xsl_file" \
#        "$OUTPUT_BASE/phpunit.xml"
