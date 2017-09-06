### PHP手册学习笔记

## 事务
在与mysql进行通信的时候，有autocommit这么一个选项。如果设置该值为0，则系统会认为所有的Sql都是事务处理，只有commit或者rollback才算结束  
myisam 竟然不支持事务    
myisam 可以通过锁表来 模拟事务操作..这样不太好吧

## shell
shell -z 代表的是 该变量是否有值  
shell -d 代表的是 判断指定的是否是一个目录  
	-f 制定的是否为文件
	-L 是否为符号链接
	-s 存在的对象长度是否为0
	-x 判断存在的对象是否可执行
	-ne 不等于

## Curl
curl 是将下载文件输出到终端，所有下载的数据都会写入stdout  

curl --silent 不显示进度信息

## shell获取机器ip
ifconfig eth1 2>>/dev/null |grep 'inet addr:'|awk  '{ print $2 }'|awk -F : '{ print $2 }'  
