# Nginx

## 简介
Nginx是一个Web服务器，也可以用作反向代理，负载均衡和Http缓存服务器。Nginx是免费开源软件，大部分的Web服务器通常使用Nginx作为负载均衡器

Nginx可以部署在网络上使用FastCGI,脚本，SCGI处理程序，WSGI应用服务器或Phusion乘客模块的动态HTTP内容，并可作为软件负载均衡器。

相比较于Apache，Nginx占用内存少，稳定性高，充分使用异步逻辑，削减上下文调度开销，所以并发服务能力强。在Linux操作系统下，Nginx使用epoll事件模型，得益于此，Nginx在Linux操作系统下效率非常高。
<!--more-->

## nginx.conf配置文件

Nginx配置文件主要分成四部分: main(`全局设置`)、server(`主机设置`)、upstream(`上游服务器设计，主要为反向代理、负载均衡相关配置`)和location(`URL匹配特定位置后的设置`)，每个部分包含若干个指令。

**Main** 部分设置的指令将影响其它所有部分的设置;  
**Server** 部分的指令主要用于指定虚拟主机域名、Ip和端口  
**Upstream** 部分的指令用于设置一系列的后端服务器，设置反向代理及后端服务器的负载均衡  
**Location** 部分用于匹配网页位置(`比如，根目录"/","/images",等等`)。  

他们之间的关系是:  Server继承Main，Location继承Server,upstream既不继承指令也不会被继承。他有自己的特殊指令，不需要再其他地方的应用。

### 通用

下面的nginx.conf 简单的实现了nginx在前端做反向代理服务器的例子，处理js、png等静态文件，jsp等动态请求转发到其他服务器 tomcat:

```
user  www www;
worker_processes  auto;

error_log  logs/error.log;
#error_log  logs/error.log  notice;
#error_log  logs/error.log  info;

pid        logs/nginx.pid;


events {
    use epoll;
    worker_connections  65535;
}


http {
    include       mime.types;
    default_type  application/octet-stream;

    #log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
    #                  '$status $body_bytes_sent "$http_referer" '
    #                  '"$http_user_agent" "$http_x_forwarded_for"';

    #access_log  logs/access.log  main;

    sendfile        on;
    # tcp_nopush     on;

    keepalive_timeout  65;

  # gzip压缩功能设置
    gzip on;
    gzip_min_length 1k;
    gzip_buffers    4 16k;
    gzip_http_version 1.0;
    gzip_comp_level 6;
    gzip_types text/html text/plain text/css text/javascript application/json application/javascript application/x-javascript application/xml;
    gzip_vary on;

  # http_proxy 设置
    client_max_body_size   10m;
    client_body_buffer_size   128k;
    proxy_connect_timeout   75;
    proxy_send_timeout   75;
    proxy_read_timeout   75;
    proxy_buffer_size   4k;
    proxy_buffers   4 32k;
    proxy_busy_buffers_size   64k;
    proxy_temp_file_write_size  64k;
    proxy_temp_path   /usr/local/nginx/proxy_temp 1 2;

  # 设定负载均衡后台服务器列表 
    upstream  backend  { 
              #ip_hash; 
              server   192.168.10.100:8080 max_fails=2 fail_timeout=30s ;  
              server   192.168.10.101:8080 max_fails=2 fail_timeout=30s ;  
    }

  # 很重要的虚拟主机配置
    server {
        listen       80;
        server_name  itoatest.example.com;
        root   /apps/oaapp;

        charset utf-8;
        access_log  logs/host.access.log  main;

        #对 / 所有做负载均衡+反向代理
        location / {
            root   /apps/oaapp;
            index  index.jsp index.html index.htm;

            proxy_pass        http://backend;  
            proxy_redirect off;
            # 后端的Web服务器可以通过X-Forwarded-For获取用户真实IP
            proxy_set_header  Host  $host;
            proxy_set_header  X-Real-IP  $remote_addr;  
            proxy_set_header  X-Forwarded-For  $proxy_add_x_forwarded_for;
            proxy_next_upstream error timeout invalid_header http_500 http_502 http_503 http_504;

        }

        #静态文件，nginx自己处理，不去backend请求tomcat
        location  ~* /download/ {  
            root /apps/oa/fs;  

        }
        location ~ .*\.(gif|jpg|jpeg|bmp|png|ico|txt|js|css)$   
        {   
            root /apps/oaapp;   
            expires      7d; 
        }
        location /nginx_status {
            stub_status on;
            access_log off;
            allow 192.168.10.0/24;
            deny all;
        }

        location ~ ^/(WEB-INF)/ {   
            deny all;   
        }
        #error_page  404              /404.html;

        # redirect server error pages to the static page /50x.html
        #
        error_page   500 502 503 504  /50x.html;
        location = /50x.html {
            root   html;
        }
    }

  ## 其它虚拟主机，server 指令开始
}
```

### 常用指令说明
#### main全局配置
nginx在运行时与具体业务功能(比如http服务或者email服务代理)无关的一些参数，比如工作进程数，运行身份等。

* `work_process auto`: 在配置文件的顶级main部分，worker角色的工作进程个数，master进程是接收并分配请求给worker处理。这个数值简单一单可以设置为cpu的核数 `grep ^processor /proc/cpuinfo | wc -l`，也就是auto的值，如果开始了ssl和gzip更应该设置成与逻辑CPU数量一样甚至2倍，这样可以减少I/O操作。如果nginx服务器还有其他服务，可以考虑适当减少。

* `worker_cpu_affinity`: 也是写在main部分。在高并发的情况下，通过设置cpu粘性来降低由于多CPU核切换造成的寄存器等现场重建带来的性能损耗。如`worker_cpu_affinity 0001 0010 0100 1000;` (四核)

* `worker_connections 65535`: 写在events部分。每一个worker进程能并发处理(发起)的最大连接数。nginx作为反向代理服务器，计算公式 `最大连接数＝worker_processes * worker_connections/4`, 所以这里客户端最大连接数是196605，这个可以增加到 196605*8都没关系，看情况而定，但不能超过后面的`worker_rlimit_nofile`。当Nginx作为http服务器时，计算公式里面是除以2。

* `worker_rlimit_nofile 10240`: 写在main部分。默认是没有设置，可以限制为操作系统最大的限制65535.

* `use epoll;` : 写在`events`部分。在Linux操作系统下，nginx默认使用epoll事件模型，得益于此，nginx在Linux操作系统下效率非常高。同时Nginx在OpenBSD或FreeBSD操作系统上采用类似于epoll的高效事件模型kqueue。在操作系统下不支持这些高效模型时才使用select

#### http服务器
与提供http服务相关的一些配置参数。例如是否使用keepalive，是否使用gzip进行压缩等。

* `sendfile on`: 开启高效文件传输模式，sendfile指令执行nginx是否调用sendfile函数来输出文件，减少用户空间到内核空间的上下文切换。对于普通应用设为on，如果用来进行下载等应用磁盘IO重负载应用，可设置为off，以平衡磁盘与网络I/O处理速度，降低系统的负载。
* `keepalive_timeout 60`: 长连接超时时间，单位是秒，这个参数很敏感，涉及浏览器的种类、后端服务器的超时设置、操作系统的设置。长连接请求大量小文件的时候，可以减少重建连接的开销，但如果有大文件上传，60s内没有上传完成会导致失败。如果设置时间过长，用户又多，长时间保持连接会占用大量资源。
* `send_timeout`: 用户指定响应客户端的超时时间。这个超时仅限于两个连接活动之间的时间，nginx将会关闭连接
* `client_max_body_size 20m` : 允许客户端请求的最大单文件字节数。如果有上传较大文件，请设置它的限制值
* `client_body_buffer_size 128k`: 缓冲区代理缓冲客户端请求的最大字节数  

***

* `proxy_connect_timeout 60` : Nginx跟后端服务器连接超时时间
* `proxy_read_timeout 60`: 连接成功后，与后端服务器两个成功的响应之间超时时间
* `proxy_buffer_size 4k`: 设置代理服务器从后端realserver读取并保存用户头信息的缓冲区大小，默认与proxy_buffers大小相同，其实可以将这个指令值设置的小一点
* `proxy_buffers 4 32k`: proxy_buffers缓冲区，nginx针对单个连接缓存来自后端realserver的响应，网页平均在32k以下的话，可以这样设置。
* `proxy_busy_buffers_size 64k`: 高负荷下缓冲大小(proxy_buffers * 2)
* `proxy_max_temp_file_size`: 当proxy_buffers放不下后端服务器的响应内容时，会将一部分保存到硬盘的临时文件中，这个值用来设置最大临时文件大小，默认1024M，他与proxy_cache没有关系。大于这个值，将从upstream服务器传回。设置为0则禁用。
* `proxy_temp_file_write_size 64k`: 当缓存被代理的服务器响应到临时文件时，这个选项限制每次写临时文件大小。`proxy_temp_path`(可以在编译时)指定写道那个目录

***
* `gzip on`: 开启gzip压缩输出，减少网络传输。
* `gzip_min_length 1k`: 设置允许压缩的页面最小字节数，页面字节数从header头的content-length中获取。默认是20.建议设置成大于1k的字节数，小于1k可能会越压越大
* `gzip_buffers 4 16k`: 设置系统获取几个单位的缓存用于存储gzip的压缩结果数据流。 4 16k代表以16k为单位，安装原始数据大小以16k为单位的4倍申请内存
* `gzip_http_version 1.0`: 用于识别http协议版本。
* `gzip_comp_level 6`: gzip压缩比，1压缩比最小处理速度最快，9压缩比最大，但处理速度最慢
* `gzip_types`: 匹配mime类型进行压缩，无论是否制定，"text/html"类型总会被压缩
* `gzip_proxied any`: Nginx作为反向代理的时候启用，决定开启或者关闭后端服务器返回是否压缩，匹配的前提是后端服务器必须要返回包含"Via"的header头
* `gzip_vary on`: 和https头有关系，会在响应头加个Vary:Accept-Encoding,可以让前端的缓存服务器缓存经过gzip压缩的页面，例如，用Squid缓存经过Nginx压缩的数据

#### server虚拟主机
Http服务上支持若干虚拟主机。每个虚拟主机对应一个server配置项，配置项里面包含该虚拟主机相关的配置。在提供mail服务的代理时，也可以建立若干server。每个server通过监听地址或者端口来区分。

* `listen`: 监听端口，默认80，小于1024的要以root启动。可以为listen *:80、 listen 127.0.0.1:80等形式
* `server_name`: 服务器名，如localhost、www.baidu.com，可以通过郑泽匹配。 
* `http_stream`: 通过一个简单的调度算法来实现客户端ip到后端服务器的负载均衡，`upstream`后接负载均衡器的名字，后端realserver以`host:port options;` 方式组织在{}中。如果后端被代理的只有一台，也可以直接写在proxy_pass.
 
#### location
* `root /var/www/html`: 定义服务器的默认网站根目录位置。如果`location`URL匹配的是子目录或文件，`root`没什么作用，一般放在`server`指令里面或者`/`下
* `index index.jsp index.html index.htm`: 定义路径下默认访问文件名，一般跟着root放
* `proxy_pass http:/backend`: 请求转向backend定义的服务器列表，即反响代理，对应`upstream`负载均衡器。也可以`proxy_pass http://ip:port`

## CGI

### CGI简介

CGI全称是“公共网关接口”(Common Gateway Interface),Http服务器与你的或其他机器上的程序进行“交谈”的一种工具，其程序需要运行在网络服务器上。

CGI可以用任何一种语言编写，只要这种语言具有标准输入、输出和环境变量。

### FastCGI简介

FastCGI像是一个常驻型的CGI，它可以一直执行着，只要激活后，不会每次都花时间去fork一次。它还支持分布式的运算，即FastCGI程序可以在网站服务器以外的主机上执行并且接受来自其他网站服务器的请求。

FastCGI是语言无关的、可伸缩架构的CGI开放扩展，其主要行为是将CGI解释器进程保持在内存中并因此获得较高的性能。众所周知，CGI解释器的反复加载是CGI性能低下的主要原因，如果CGI解释器保持在内存中并接受FastCGI进程管理器调度，则可以保持良好的性能、伸缩性、Fail－Over特性等等





