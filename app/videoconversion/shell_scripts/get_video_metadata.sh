#!/bin/bash
# get_video_metadata.sh: takes a file as an argument and returns it's meta data text from ffmpeg

input="$1"

# the only way to get the metadata will return an error so we redirect stderr
/usr/local/bin/ffmpeg -i $input 2>&1
