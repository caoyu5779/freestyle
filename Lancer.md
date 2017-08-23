#Lancer流程
>工欲善其事，必先利其器

##命令
在Lancer中，会用到很多Shell相关的命令。

**shell中的0，1，2** : 0,标准输入，1，标出输出，2:标准错误输出

**eval** : 重新运算求出参数内容。再次执行

**export** : 在shell执行的时候，会提供一组环境变量。export可以新增、修改或者删除环境变量，供后续执行的程序使用。但是export的效力仅限于该次登陆操作。  
> -f 代表[变量名称]中为函数名称

**cut** : cut剪切数据使用的。以每一行为一个处理对象。  
cut -b 按字节 -c按字符 -f按域 －d指定分隔符 与awk相似

**which sh** : 在Linux系统中，sh是bash的一个软链接。which sh 运行结果 /bin/sh  

__$$__ : echo $$ 标示当前shell进程的id，即pid

__$*__ : 传递給脚本或者函数的所有参数。

**2>>/dev/null** : 把错误流写进/dev/null中。unix中，0代表标准输入流(stdin),1代表标准输出流(stdout),2代表标准错误流(stderr). **/dev/null**是类Unix系统中的一个特殊文件设备，作用是接受一切输入它的数据，并丢弃这些数据。通常被看作垃圾桶。

**inet addr** ifconfig后，inet addr里面的值分别是 ip地址／网关／子网掩码

**$?** : 上个命令的退出状态，或函数的返回值。

**curl --silent** : 静音模式，不显示错误与进度

**head -n 1** : 显示文件的第1行

**php -r** : 在命令行中直接执行php代码

**tr** : Linux中tr通常对来自标准输入的字符进行替换、压缩和删除。可以将一组字符变成另外一组字符。tr 'A-Z' 'a-z' 则为大小写替换。
tr -d 删除第一个字符集的字符

##流程
1.定义变量，包括获取数据地址，数据确认地址，数据完成地址。UA。设定时间。定义文件夹，数据文件夹，日志文件夹。日志文件名。运行地址。

2.定义变量方法，将函数定义为变量。

3.获取机器ip，获取1号网卡的ip。如果没有，就获取0号网卡的ip，将ip赋值給变量。  

```
client_ip=$(ifconfig eth1 2>>/dev/null |grep 'inet addr:'|awk  '{ print $2 }'|awk -F : '{ print $2 }')

if [ $client_ip"x" == "x" ]; then
    client_ip=$(ifconfig eth0 2>>/dev/null |grep 'inet addr:'|awk  '{ print $2 }'|awk -F : '{ print $2 }')
fi
```
4.如果不存在SERVER_ADDR则给他赋值当前ip

5.如果不存在调用框架配置文件的脚本，则异常推出

6.执行配置文件，与框架相关联

7.如果执行配置文件出错，即返回码不为0，则报错退出

8.如果配置文件中，没有当前机器的ip，则退出

9.如果不存在日志文件夹则创建日志文件夹，并记录日志

### 拉取文件
10.获取要执行的tasks，使用curl的静默模式(-s)请求获取tasks接口，如果没值，停20ms，重试。如果有返回，则判断返回参数，是不是100000，如果不是，则重试。

11.如果非正常退出，则Lancer客户端也退出。

### 文件处理
12.输出tasks的返回值。同时使用php -r在命令行中直接执行php代码。代码逻辑为:获取命令行输出，获取到data里面的值。然后将data里的值，echo出来。知道读不出来为止，同时将task_id queue_id command 作为变量保存下来
```
 echo "$tasks" | $php_executor -r '$in=file_get_contents("php://stdin");$in=json_decode($in,true);foreach($in["data"] as $t){echo trim($t["task_id"])."@".trim($t["queue_id"])."@".trim($t["command"],"&; \t\n\r\0\x0B")."\n";}' | sed 's/\([\\\"]\)/\\\1/g' | while read line; do [ ! -z "$line" ] && exec_task "$line" ; done
 ```
 
 然后后台执行exec脚本。
 
##执行
父shell中包含子shell，里面export的值，是可以在子shell中使用的。

1.建立对应的对应的正确｜错误的输出文件  

2.调用确认接口，为对应的task进行确认。传入数值为queue_id,与task_id.

3.根据返回值，记录正确或者错误日志。

4.再次执行从接口取出的(设定好的)crontab指令。

5.获取执行的退出码，记录执行时间。

6.将标准输出与错误输出都格式化输出。这个挺屌的
```
lancer_stdout=$(dd if="${LANCER_LOG_STDOUT}" bs=1000 count=1 2>/dev/null | od -An -tx1 | tr ' ' % | tr -d '\n')
```
7.然后调用完成接口，完成本次shell全部执行，并且更新数据。