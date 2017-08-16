
#!/bin/sh
chmod -R 777 *
for i in $(seq 1 10)
do
    ./daily_active_user.sh $[$i+1] $i
done