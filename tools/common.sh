#!/usr/bin/env bash

log() {
    RED='\e[31m'
    GREEN='\e[32m'
    YELLOW='\e[93m'
    BLUE='\e[95m'

    case $2 in
        wait)
            printf "${BLUE}${1}\e[0m\n";;
        progress)
            printf "${GREEN}${1}\e[0m\n";;
        info)
            printf "${YELLOW}${1}\e[0m\n";;
        error)
            printf "${RED}${1}\e[0m\n";;
        *)
            printf "${1}";;
    esac
}

spinner() {
   count=0
   total=100
   pstr="[==========================================]"

   while [ $count -lt $total ]; do
     sleep 0.1 # this is work
     count=$(( $count + 1 ))
     pd=$(( $count * 73 / $total ))
     printf "\r%3d.%1d%% %.${pd}s" $(( $count * 100 / $total )) $(( ($count * 1000 / $total) % 10 )) $pstr
   done
    printf "\e[0m\n"
}



