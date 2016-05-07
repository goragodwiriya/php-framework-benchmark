#!/bin/sh

base="http://localhost/php-framework-benchmark"

cd `dirname $0`

. ./list.sh

cd benchmarks

if [ "$1" = "hello" ] || [ "$1" = "all" ] || [ $# -eq 0 ]; then
    export targets="$list"
    sh hello_world.sh "$base" "hello"
    php ../bin/show_results_table.php "hello"
fi
if [ "$1" = "orm" ] || [ "$1" = "all" ]; then
    export targets="$orm"
    sh hello_world.sh "$base" "orm"
    php ../bin/show_results_table.php "orm"
fi
if [ "$1" = "select" ] || [ "$1" = "all" ]; then
    export targets="$select"
    sh hello_world.sh "$base" "select"
    php ../bin/show_results_table.php "select"
fi
if [ "$1" = "cms" ] || [ "$1" = "all" ]; then
    export targets="$cms"
    sh hello_world.sh "$base" "cms"
    php ../bin/show_results_table.php "cms"
fi
if [ "$1" = "php7" ] || [ "$1" = "all" ]; then
    export targets="$php7"
    sh hello_world.sh "$base" "php7"
    php ../bin/show_results_table.php "php7"
fi
if [ "$1" = "db" ] || [ "$1" = "all" ]; then
    export targets="$db"
    sh hello_world.sh "$base" "db"
    php ../bin/show_results_table.php "db"
fi