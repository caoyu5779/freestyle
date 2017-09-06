### Scalability 学习笔记

scalability 翻译为 可扩展性

选择设计为分布式的原因：  
1.Data Scalability: 单台机器容量不足以承载所有资料  
2.Computing Scalability: 单台机器的运算能力不足以及时完成运算

分布式设计会造成的问题:  
1.牺牲效率  
2.牺牲AP弹性，什么是AP弹性?有些能在单机上执行的运算，无法轻易在分布式环境中完成。  
3.牺牲运维能力，分布式系统问题常常很难重现，也会很难追踪。  

系统设计上的权衡:
1.cpu使用效率优化或IO效率优化  
2.读取优化或者写入优化  
3.吞吐量优化或者延迟优化  
4.资料的一致性或者资料的可得性  
根据不同的权衡(tradeoff)会得到不同的系统架构

## 系统
1.资料系统  
2.运算系统  

分布式系统的两个问题根源在于，划分与复制。  
资料的划分常用的方法有：  
1.Round-Robin:资料轮流进入多台机器。好处是负载均衡，坏处是不适合session。变换方法可以使用线程池，每个机器规定配几个线程，可以避免运算长耗时。  
2.Range：不好的方式，根据key分配，会遇到热点数据问题  
3.Hash：用了一致性hash后，可以很均匀的分布，难点在于hash的规则不好掌握。很难通过hash规则让某些资料必须在一起。  
4.Manual：手动，谁选谁sb。  

在分布式系统中，如果查询费时，可以尽量的分散；如果查询很快，尽量集中在少数机器上进行处理。

原因：查询很快的话，分散处理会增加效能降低到风险。因为要在所有的机器都回传资料以后才进行运算，所以运算时间是MAX(所有机器的处理事件)。机器越多，效率的增加会比效率降低的风险小。

举例：参与会议的人越多，就需要花费更多的时间进行协调，因为要等回复最慢的人。

## 常用(推荐)分布式的管理工具
ZK(zookeeper)。通过使用ZK来维护metadata是许多分布式系统的普遍做法。ZK有自己的hash和一致性来保证数据，而且在生产环境中，一次要用2N+1个节点，只要不大于n个节点挂掉，都可以正常服务。Zk里面的数据很重要，所以要备份，至少3份。

## 资料复制

Quorum 算法.  
算法简述可见下面链接  
http://www.cnblogs.com/netfocus/p/3622184.html  
R+W>N，可以控制读写效率，然后牺牲另外一个operation来换取数据一致性。  
分布式的数据更新，通常要积累一些更新或间隔一段时间后，才回批量更新。  
常见的复制有三个副本。除了原始资料外，还会有同一个rack(架)或者数据中心的副本，另一个rack或者数据中心的副本。

副本大部分是不允许写入的。纯粹是为了增加读的分布性、效率、可用性。同时只有一个master负责写入，其他的slave副本，只负责读。  
如果遇到允许副本写入的系统，像cassandra。规则会根据timestamp订输赢。所以需要NTP(Network Time Protocol) ,last write win. 但是在没有协调之前，还是会数据时间不一致。

## 无强一致性及无法节点执行顺序带来的问题
数据不一致。  
大部分的系统，只允许一个master节点写，其他的slave节点可以读。更极端的会有，只有master可读写。

## 最终一致性
无论怎么着都会一致的，这不是扯犊子呢么

## CAP原则
CAP原则：在分布式系统中，Consistency(一致性),Availability(可用性),Partition tolerance(分区容错性)，三者不可兼得

C(strong Consistency) 强一致性。在任何时候，从簇中的任何两个节点得到的状态都是一样的  
A(Availability) 若一个节点没有坏掉，那它就必须能够正常服务  
P(Partition Tolerance): 如果一个丛集因网络问题或者节点问题，被切割成了两个或多个不完整的子簇时，系统还能正常运转。

在分布式系统中，以上三个特性最多只能有两个同时存在


Two phase commit 两阶段提交，所有的节点同意后才能存入资料。   

一般的分布式系统，都要求有P，分区容错性。  

但出现分区不一致的时候，会出现不一致的情况，更有甚者会出现 都认为自己是master的情况，都可以写。
为了避免不一致，则必须停掉其中的一个节点。

CAP 原则的示意图，可以在：  
Source: http://www.w3resource.com/mongodb/nosql.php里面看

## ZooKeeper
ZooKeeper的常见用法：  
1.共享Metadata  
2.监控成员节点的状态，维护丛集中的成员名单  
3.协助选出丛集中的leader  

Zk的资料是树状组织形态。节点叫做Znode，可以在Znode中存放资料  

zk有notification机制，可以在znode里面的资料更新时，对事先注册过该znode watcher的process进行通知。  

有一种znode叫做ephemeral node，我叫它(短暂节点)，用来监控成员的状况。这个znode与建立znode的成员的session状况是联动的。如果这个session一段时间内没有回报(heart beat)，这个znode节点就会删除。如果有成员在这个znode上设定watcher，就会在此节点挂掉的时候，收到通知。

所有的成员，都会对master对应的ephemeral node注册watcher，所以在master失效时，所有成员会重新选一个leader。

Zk能够保证 global order。因为只有leader能处理写入要求。  
Quorum是指成员数达到最低投票门槛的成员集合。Zk的成员有两种角色，Leader，Follower。Leader Election的目的就死选出Quorum中的leader。  

Quorum中的最低门槛指的是，成员数要大于Zk节点的一半数量。  
假设有5个节点，Quorum里最少要有3个成员。这样保证了，当丛集被partition成两半，其中一个partition的节点数不足以形成Quorum。不成Quorum则不对外服务。所以一个簇中，最多只能有一个Quorum对外服务，也就不会发生不一致的情况。  

## Apache Kafka
Kafka是一个分布式队列的实现，很多流计算平台都支持kafka做为数据源。  
###Kafka的特色：  
1.分散式结构，易扩展。  
2.基于硬盘空间，并且可以避免随机存取  
3.存储空间大，就算队列中的资料已消耗，也可以不删除。好处是 其他新加入的consumer可以处理过去的资料。如果有批处理的consumer，可以一次拉取大量的资料，提高批处理的效率。  
4.对资料的包装为轻量级的(啥意思)，可压缩。
5.因为可以直接处理资料，可以直接使用操作系统的page cache(页高速缓存).

### Kafka的架构：
基本上kafka是一个broker的角色。broker 简单的理解就是服务端，把消息从传送端 传递到 接收端。  

一组资料流称为一个topic，为了避免topic的资料量过大，所以topic可以分成多个partition，每个partition会在不同的节点上。  

Producer必须自己决定将资料送到那个partition，在Api中有一个参数可以让使用者制定partition key，然后producer Api用hash方式决定partition。  

kafka可以弹性支持p2p和pub／sub(发布及订阅)两种队列模式。主要是透过一个Consumer Group的抽象，每个Consumer Group可以当作一个虚拟的consumer，但可以由多个实体的consumer组成。p2p就是将所有的 consumer划分成同一个Consumer Group；而Pub/Sub是将不同的Pub的Sub分成不同的Consumer Group。

好处是，每一个Consumer只会同时bind一个partition，也就是说，一个consumer只会找一个partition拉资料。 一个partition只能同时被同一个 consumer group中的 consumer消费，保证了这个partition对于同一个 consumer group 来说，不会被并发取。

### Kafka的限制：  
1.一个Partition应该是只能绑定一个consumer的。但是一个consumer可能会绑定多个partition。如果consumer的数量大于partition的数量，则会有一部分的consumer获取不到数据。  

2.各个consumer的消耗速度不同，partition的消耗速度也会失衡。即有的partition已经消耗到新的，有的paration还在消耗旧的。  

3.kafka是有rebalance功能的,在consumer queue中，新加consumer，会触动rebalance，会重新分配partition与consumer之间的对应关系。  

4.基于以上，每个partition实际上可以看成一个独立的queue，每个partition保证自己的local order  

简单的说，Kafka假设，AP是不需要total order的，抑或是 ap只需要by－partition的local order，只需要做好partition，就可以维持好时序的资源消耗。  

### Kafka的replication机制
kafka是以partition为单位的。就是让replica 取订阅要追踪的partition。  
在每一个replica set里，只会有一个master，这个master负责读写，其他的salve都是用户后备。每个replica set会维持一个ISR名单，若master挂掉，会从这个ISR中挑出一个新的master。  
在写入的时候，虽然是根据master作为入口，进行写入，但是必须要等到master的资料同步到所有ISR，这个资料才算commit，才能被consumer看到  

### ack
ACK(Acknowledgement)即确认字符，在数据通信中，接收站发給发送站的一种传输类的控制字符。表示发来的数据已经确认接收无误。  
在TCP／IP协议中，如果接收方成功的接收到数据，那么会回复一个ACK数据。ACK有自己固定的格式、长度、大小。由接收方回复給发送方。

Kafka的ack只要确保 message deliver semantics信息可靠性保证。  

所以kafka天生就会有可靠性保证的问题。就像网络，有时候允许遗失封包(UDP)，如果不能遗失封包(TCP)，就要有重送和检查重复的机制。

## 科层组织
就如现在啊

## 类比
让我们用科层组织来类比分散式资料系统,作为分散式资料系统的小节吧。  我们从 partition 和 replication 谈起,partition 就像科层组织,为了避免规模过大的管理困 难,所以切割成多个可相对独立运作的单位来分而治之。但问题就是协调困难,尤其是在 一个沟通管道不稳定的环境中。replication 像是职位的代理人,有些代理人可以很快上手, 还可以帮忙分摊一些负担;不过有些代理人完全就是备援角色,甚至还有些代理人完全没 进入状况。怎麽把这样一个障碍重重又各自为政的组织联合起来,让它能如臂使指,那就 是分散式资料系统想做的事。  我们还介绍了一些重要工具,Zookeeper 像是个专人管理的中央布告栏,帮助组织间沟通 协调,确保大家的认知是相同的。Zookeeper 非常尽责,你可以先跟他说你关心哪些公告, 那些公告有更新的话还会主动通知你~  Kafka 像是个高效的公文传送工具,让单位之间的资料能顺畅流通。但 Kafka 可不会主动 通知你有公文,你要自己去检查你的公文箱。不过 Kafka 会帮忙保留一些历史公文,所以 只要不要拖太久才去检查公文箱,基本上公文都还找的回来。  

我觉得这个类比很形象

## 分布式运算系统
Hadoop是典型的数据并行性系统，将资料切成小块，每一块平行处理来增加处理时效。

Stream Computing 结合了 数据并行性与Pipeline(管道)

作业系统常用的两种内部进程通信方法：  
1.共享内存：在内存中交换资料，确定是，多人用需要排队，效率低。现在有了新的方法，就是乐观锁、多版本控制。但是这样都有额外费用。  
2.信息传递(message passing)：所有的进程间，都透过讯息的方式来交换资料，缺点就是缺乏全排序。  

分布式的运算系统的两种基 本思路：  
1.共享数据存储(Shared data store)：找一个大家都能access的data store来存资料，这个数据存储可能是某种分散式资料系统。
  
2.Peer Communication(同行通信):透过某些高效的通讯协定在各节点间交换讯息，通常是不闭塞的通讯方式，而且还要用高效能的序列化框架。  

## Stream Computing的应用范围
适用于大量event涌进的应用，最常见的是活跃性分析。  
还有alert，分析log。 
即时扣款。

## Stream Computing特性
Stream Computing 是设计給需要低延迟的应用。hadoop这种批量处理的方式，需要等待所有的数据都处理完毕以后才一次性的推出，这样就会产生延迟。为了减少了延迟，流计算 将处理的颗粒度 减小到record，且将处理过程分为好几个阶段。通过管道的方式，只要前一阶段处理完的record，马上进行下一阶段，减少延迟。

通过叙述就能想到这样的吞吐量肯定不如批量处理。为了解决这样的问题，流计算框架都会有内建分布式。每一个阶段的处理方式都是可以扩充的，意思是，可以第一个阶段用10个线程，第二个阶段用5ge。用效率换扩展性，在用扩展性弥补效率。

## Stream Computing框架
1.Apache Strom  
2.Apache Samza  
3.Apache Spark Streaming  

## Stream Computing框架的组成角色
1.处理客户端提出的运算要求，将运算工作拆成小单位任务，分派到各个运算节点上。  
2.管理运算节点，就是leader/master，每个机器上需要一个或者多个leader  
3.运算节点，实际执行运算，并将运算结果通过高效率方式传递給下一个阶段的运算节点  
4.资料管道，担任运算节点之间的中继站，或者输入资料的中继站。如果consumer 速度慢于producer速度时负责buffer。
5.丛集协调中心，分则协调及交换整体丛集的状态

## 如何追踪每一个Record的处理进度
每一个record在最源头都会被指派一个messageId,在每次处理完成后产生的新资料的后续传送中，都会带上这个id，从而分辨消息源头。

在每个阶段处理完input data，产生output data往下游送时，会向Ackor 发出ack或者fail 的回报，并带有 64bit Id。在正常情况下，ackor会收到两次同一个id，表示该资料处理完。ackor采用的方式是为每一个record维护一个初始值为0的64位value，每收到一个id回报，将value<-value XOR id。如果每次id都出现两次，那么value就会回到0，则改record相关的处理都完成了。






