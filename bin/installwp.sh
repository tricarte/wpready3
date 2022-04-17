#!/usr/bin/env bash
if command -v wpsite > /dev/null 2>&1; then
    WPSITE=$(command -v wpsite)
else
    echo "Err: wpsite is not installed."
    echo "Follow installation instructions at:"
    echo "https://github.com/tricarte/wpsite"
    exit 1
fi

$WPSITE install
