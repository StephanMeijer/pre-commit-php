#!/usr/bin/env bash                                                                                                                                                                                               

set -e                                                                                                                                                                                                            

if [ "$#" -eq 0 ]; then                                                                                                                                                                                           
    echo "Please provide an npm command to run as an argument."                                                                                                                                                     
    exit 1                                                                                                                                                                                                          
fi                                                                                                                                                                                                                

npm_cmd="$1"                                                                                                                                                                                                      
shift                                                                                                                                                                                                             

echo "Running 'npm $npm_cmd'"                                                                                                                                                                                     

npm "$npm_cmd" "$@"
