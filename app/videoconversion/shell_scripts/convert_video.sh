#!/bin/bash
# convert_video.sh: takes a file as an argument and converts it into mp4 and webm

input="$1"
title="$2"
size="$3"

# assign folder to everything before the final / in input
folder="${input%/*}"

# mkdir -p ${folder}/exports

 #For webm -b:v will try to reach the specified bit rate on average, e.g. 1 MBit/s.
 #Choose a higher bit rate if you want better quality. Note that you shouldn't leave out the -b:v option as the default settings will produce mediocre quality output.
 #the target bitrate becomes the maximum allowed bitrate spcified by the -crf parameter. By default the CRF value can be from 4–63, and 10 is a good starting point. Lower values mean better quality
/usr/local/bin/ffmpeg -i $input -s $size -pix_fmt yuv420p ${folder}/${title}.mp4 -s $size -c:v libvpx -crf 10 -b:v 1M -c:a libvorbis ${folder}/${title}.webm
# ffmpeg -i mst_esignal_05_tvtv_prores.mov -c:v libvpx -crf 10 -b:v 1M -c:a libvorbis mst_esignal_05_tvtv_prores.webm







