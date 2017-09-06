#消息队列
## RocketMQ
### 专业术语 

>__追本溯源__    

 
* **Topic** 消息第一类型。一条消息必须有一个Topic，这个接触过Mq的都应该清楚  
* **Tag** 消息的第二类型。可以理解为第一类型的消息分支。Tag是非必需的  
* **Queue** 一个Topic下，可以设置多个Queue。当发送消息时，需要指定消息的Topic。RocketMQ会轮询该topic下的所有队列，发送消息  
* **Producer**与**Producer Group** Producer是消息队列的生产者。Producer主要是用来生产与发送消息的，一般针对业务。  
* **Consumer**与**Consumer Group** 消息消费者，异步消费消息  

 > **Push Consumer** Consumer的一种，应用通常向Counsumer注册一个Listener，一旦收到消息，Consumer对象立刻回调Listener接口方法[这个挺好]
 > **Pull Consumer** Consumer的一种，应用通常调用Consumer的拉消息方法从Broker拉取消息，主动权由应用控制。
* **Broker** 消息的中转者，负责存储和转发消息。消息队列服务器，提供了消息的接收、存储、拉取和转发服务。**Broker**是MQ的核心，要保证高可用。
* **广播消费** 一个消息被多个Consumer消费，即使这些Consumer属于同一个Consumer Group，消息也会被Consumer Group中的每个Consumer消费一次。
* **集群消费** 一个Consumer Group中的Consumer实例平均分摊消息。例如一个Topic有9条消息，Group有3个实例，每个实例只消费其中的3条消息

* **NameServer** 名称服务，功能:   
>1. 接收broker请求，注册broker的路由信息。  
>2. 接收client的请求，根据某个topic获取其到broker的路由信息。NameServer没有状态，可以横向扩展。  
>3. 每个broker启动时会在NameServer中注册。Producer在发送消息前会根据Topic到NameServer获取路由信息；Consumer也会定时获取Topic路由信息。

## OverView

![avatar](https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1502959836898&di=8c54c8fbabe3832c1046b6864247e078&imgtype=0&src=http%3A%2F%2Fwww.lupaworld.com%2Fdata%2Fattachment%2Fportal%2F201403%2F12%2F153201x8ai0dxebe3xkejf.png)

**Producer**向一些队列轮流发送消息，队列集合称为Topic，Consumer如果消费则消费这个Topic对应的所有队列；如果集群消费这个Topic，则每个Consumer平均消费这个Topic对应的队列集合。

**物理部署图**  
![avatar](https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1503558915&di=0a3c400d2112d342798ee44e62c5caa9&imgtype=jpg&er=1&src=http%3A%2F%2Fimage.kejixun.com%2F2017%2F0723%2F20170723112958957.jpg)

**RocketMQ网络部署特点**:  
> * NameServer是一个几乎无状态的节点，可集群部署，节点之前无任何信息同步  
> * Broker部署相对复杂，Broker分为Master和Slave，一个Master可以有多个Slave，Master与Slave的对应关系是通过指定相同的BrokerName，不同的BrokerId来进行指定的。BrokerId＝0标示Master，非0为Slave。Master可以部署多个。每个Broker与Name Server集群中的所有节点建立长链接，定时注册Topic信息到所有的NameServer
> * Producer与Name Server集群中的某一个节点(随即选择)建立长连接，定期从Name Server取Topic路由信息，并向提供Topic服务的Master建立长连接，且定时向Master发送心跳。Producer完全无状态，可集群部署。
> * Consumer与Name Server集群中的一个随机节点建立长链接，定期从Name Server取Topic路由信息，并向提供Topic服务的Master、Slave建立长链接，且定时向Master、Slave发送心跳。

## 正文
>文中的MQserver与上文中的Broker相同

### 背景
分布式消息系统作为实现分布式系统可扩展、可伸缩性的关键，需要具有高吞吐量、高可用性等特点。而谈到消息系统设计，就需要关注  
1.消息的顺序问题  
2.消息的重复问题  


