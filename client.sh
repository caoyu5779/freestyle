#!/usr/bin/env bash

source /etc/profile

export INTERFACE_PULL=http://a.b.com/interface/pull
export INTERFACE_CONFIRM=http://a.b.com/interface/confirm
export INTERFACE_COMPLETE=http://a.b.com/interface/complete
export UA="v0.1"

export CURRENT_TIME=$(date '+%Y%m%d-%H-%M-%S')
export TODAY_TIME=$(echo $CURRENT_TIME | cut -d '-' -f1)

export DATA_FOLDER=/data1/www/data/a
export LOG_FOLDER=${DATA_FOLDER}/client/${TODAY_TIME}
export LOG=${LOG_FOLDER}/${TODAY_TIME}.log
export APP_PATH=/data1/www/htdocs/client/applications/

tasks=
client_ip=
exec_script=/data1/www/htdocs/client/applications/cron/c.php
shell_path=$(which sh)

function client_log()
{
    echo "[$(date '+%Y-%m-%d %H:%M:%S')]" "[$$]" "$*" >> ${LOG}
}

export -f client_log

function fetch_tasks()
{
    client_log "start fetch tasks"
    for retry in {1..3}
    do
        tasks=$(curl --silent --user-agent "${UA}" "${INTERFACE_PULL}?client_ip=${client_ip}")
        if [-z "$tasks"];then
            client_log "fetch failed . retry=${retry} tasks=${tasks}"
            usleep 1000
            continue
         fi

          code=$(echo "tasks" | head -n 1 | sed 's/^{"code":"\([0-9]\+\)".*$/\1/')
          if ("$code" -ne "100000");then
            client_log "fetch failed.retry=${retry} tasks=${tasks}"
            usleep 1000
            continue
          fi
          client_log "fetch done.retry=${retry} tasks=${tasks}"
          return 0;
    done
    tasks=
    return 1;
}

client_ip=$(ifconfig eth1 2>>/dev/null | grep 'inet addr:' | awk '{print $2}' | awk -F : '{print $2}')
if [$client_ip"x" == "x"];then
    client_ip=$(ifconfig eth0 2>>/dev/null | grep 'inet addr:' | awk '{print $2}' | awk -F : '{print $2}')
fi

export SERVER_ADDR=${client_ip}

if [-z SERVER_ADDR];then
        export SERVER_ADDR=${client_ip}
fi

if [! -f $APP_PATH/script/config.php];then
    exit 1
fi

cron_exec_servers=`php $APP_PATH/script/config.php`

if [$? -ne 0];then
    exit 1
fi

if [`echo $cron_exec_servers | grep $client_ip |wc -l | awk '{print $1}'` -eq 0];then
    exit 0
fi

if [! -e ${LOG_FOLDER}];then
        mkdir -p ${LOG_FOLDER}
        client_log "mkdir -p ${LOG_FOLDER}"
fi

