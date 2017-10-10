!#/bin/sh
mycount=0; while (($mycount<10));do mysql  -e"
select sum(gift_count) from table $mycount where day='20170926'">>count.txt;((mycount=$mycount+1));done;