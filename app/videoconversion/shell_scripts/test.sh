#!/bin/bash



input="$1"
title="$2"
size="$3"

# assign folder to everything before the final / in input
folder="${input%/*}"

# mkdir -p ${folder}/exports
echo ${folder}

