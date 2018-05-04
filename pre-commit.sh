#!/bin/sh

echo "Checking the coding style for possible issues..."

composer cs-check

EXIT_STATUS=$?

echo "done!"
echo ""

if [ $EXIT_STATUS -eq 0 ]; then
    echo "All good! No coding style issues found :)"
else
    echo "Coding style issues detected! To fix, execute: composer cs-fix"
fi

exit $EXIT_STATUS
