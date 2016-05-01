#!/bin/sh

cd `dirname $0`
. ./_functions.sh

base="$1"
bm_name=`basename $0 .sh`

results_file="output/$2/results.$bm_name.log"
check_file="output/$2/check.$bm_name.log"
error_file="output/$2/error.$bm_name.log"
url_file="output/$2/urls.log"

cd ..

mv "$results_file" "$results_file.old"
mv "$check_file" "$check_file.old"
mv "$error_file" "$error_file.old"
mv "$url_file" "$url_file.old"

for fw in `echo $targets`
do
    if [ -d "$fw" ]; then
        echo "$fw"
        . "$fw/_benchmark/hello_world.sh"
        if [ "$2" = "hello" ]; then
            benchmark "$fw" "$url" "$2"
        fi
        if [ "$2" = "orm" ]; then
            benchmark "$fw" "$orm" "$2"
        fi
        if [ "$2" = "select" ]; then
            benchmark "$fw" "$select" "$2"
        fi
        if [ "$2" = "cms" ]; then
            benchmark "$fw" "$cms" "$2"
        fi
        if [ "$2" = "db" ]; then
            benchmark "$fw" "$db" "$2"
        fi
        if [ "$2" = "composer" ]; then
            benchmark "$fw" "$composer" "$2"
        fi
        if [ "$2" = "php7" ]; then
            benchmark "$fw" "$php7" "$2"
        fi
    fi
done

cat "$error_file"
