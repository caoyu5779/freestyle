#PHP基础

## 包含语句
include、include_once  
require、require_once  

include和require遇到包含不存在的文件的时候，报错级别不同。include报一个警告，require报一个致命错误  

include包含的文件不存在的时候，程序会继续往下执行。require不会往下执行  

带_once的包含语句，在运行的时候会检查这一行的前面有没有包含，如果有，本次就不再包含，如果没有，就包含。