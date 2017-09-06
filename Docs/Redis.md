# Redis
## 简介
redis是一种高级的key:value存储系统，其中value支持五种数据类型：  
>1. 字符串(String)  
2. 字符串列表(Lists)  
3. 字符串集合(Sets)  
4. 有序字符串集合(Sorted Sets)  
5. 哈希(Hashes)  

Key不要太长，尽量不超过1024字节，不仅消耗内存，而且会降低查找效率;

Key也不要太短，太短的话，会降低可读性

在一个项目中，key最好使用统一的命名格式。uid:111:pwd:abc

##String

String是一个很基础的数据类型，也是任何存储系统都必备的数据类型。

```
set mystr "hello world"
get mystr

```
字符串类型的用法，非常简单，因为二进制安全的，所以可以把一个图片文件的内容作为字符串来存储。  

另外，还可以通过字符串类型进行数值操作:  

```
set mynum "2"
get mynum
incr mynum
get mynum  
"3"
```
在数值操作时，redis会将字符串类型转换为数值。  

由于INCR等指令本身就具有原子操作的特性，所以完全可以利用redis的incr、incrby、decr、decrby等指令来实现原子计数的效果。
>假设某个场景下，有3个客户端同时读取mynum的值，并且給其＋1，那么最后mynum的值一定是5。很多网站用redis来进行业务上的统计技术需求。  

其他命令:  
**SETNX** :SET if not exists(如果不存在，则SET), **Setnx**可以用作加锁原语。
>比如说，要对关键字(key)__foo__加锁，可以尝试一下方式:  

```
setnx lock.foo <current Unix time + lock timeout +1>

```
>可以通过del lock.foo来释放锁。  
>
> **处理死锁**:如果因为客户端失败、崩溃或其他原因没有释放锁的话，怎么办? 这种状况可以通过检测发现，因为上锁的key保存的是Unix时间戳，加入key值的时间戳小于当前时间戳，表示锁已经不再生效。  
> 如果有多个客户端同时竞争的时候，就不能通过删除锁来进行解决了。
> >C1和C2读取lock.foo并检查时间戳，setnx都返回0，因为他已经被C3锁上了，但C3在上锁后就崩溃了。  
> > 1. C1向lock.foo发送Del命令。  
> > 2. C1向lock.foo发送setnx命令并成功。  
> > 3. C2向lock.foo发送Del命令。  
> > 4. C2向lock.foo发送setnx命令并成功。  
> > 5. C1和C2都获得了锁。  
> 但是以下算法可以避免以上的问题。  
> > 1.C4向lock.foo发送setnx命令。  
> > 2.因为C3崩溃，还锁着lock.foo，所以redis向C4返回0.  
> > 3.C4向lock.foo发送GET命令，查看lock.foo的锁是否过期。如果不，休眠，重试。  
> > 4.如果lock.foo内的unix时间戳比当前时间戳老，则C4执行一下命令:
> >
```
GETSET lock.foo <current Unix timestamp + lock timeout + 1>
```
> >因为getset的作用，C4可以查看GETSET的返回值，确定lock.foo之前存储的旧值仍是哪个过期的时间戳，如果是的话，那么C4获得锁。

**SETEX** : 将值value关联到key，并将key的生存时间设为seconds(以秒为单位)。如果key已经存在，setex命令将会覆写旧值。
这个命令类似于一下的两个命令:

```
set key value
expire key seconds
```
**setex** 是一个原子性的操作，关联值和设置生存时间两个动作会在同一时间内完成，该命令在redis用作缓存的时候，非常实用。  

**Mset** 同时设置一个或者多个key-value对。当发现同名的key存在时，mset会用新值覆盖旧值，如果不希望覆盖同名的key，请使用msetnx

**MsetNx** 类似于setnx，不做赘述。

**Append** 如果key存在，并且是一个字符串，append会将value追加到key原来的值之后。如果不存在，则append就简单的将给定的key设为value，就像执行set key value一样。

**Mget** 返回所有(一个或者多个)给定的key值。  

**GetRange**: getrange key start end.返回key中字符串值的子字符串，字符串截取的范围由start和end两个偏移量决定。负数表示从最后开始计数。

**GetSet**: getset key value  
将给定的key值设为value，并且返回key的旧值。  

> getset可以和incr组合使用，实现一个有原子性复位操作的计数器。  
> 举例来说，每当某个时间发生时，进程可能对一个名为 mycount的key调用incr操作，通常我们还要在一个原子时间内完成获得计数器的值和将计数器复位为0两个操作。  
> 可以用命令 getset mycounter 0来实现这一目标。

```
incr mycount
(integer) 11
getset mycount 0
"11"
get mycount
"0"
```
**Decr** decr key.将key中存储的数字值减一。  
**Decrby** decrby key decrement   
将key所存储的值减去减量decrement。如果key不存在，以0为初始值，然后执行decrby操作。

**Incr** incr by 将key中存储的数字值加一  
**Incrby** incrby key increment 将key所存储的值加上增量increment

##Lists
redis的另外一个重要的数据结构是lists(列表)。

redis的lists底层实现不是数组，而是链表。也就是说无论是10个元素的链表，还是1百万个元素的链表，从头尾插入一个元素的时间复杂度是常数级别的，也就是Lpush｜Rpush插入新元素的时间是相同的。

缺点为:定位某个元素的速度很慢。

常用的操作包括: Lpsuh,Rpush,Lrange,Lpop

```
lpush mylist "1"
(integer)1
rpush mylist "2"
(integer)2
lpush mylist "0"
(integer)3
lrange mylist 0 1
1) "0"
2) "1"
lrange mylist 0 -1
1) "0"
2) "1"
3) "2"
```

Lists的应用场景非常广泛:  
1.可以利用lists来实现消息队列，而且可以确保先后顺序。  
2.利用Lrange可以方便实现分页的功能  
3.在Blog中，每篇Blog的评论可以存入一个单独的list中。  

**lpush** 将一个或多个值插入到列表表头  

**lpushx** 将value插入列表key的表头，当且仅当key存在并且是一个列表  

**lpop** lpop key，移除并返回列表key的头元素。 
 
**rpop** rpop key,移除并返回列表key的尾元素。  

**blpop** 是列表的阻塞式(blocking)弹出原语。如果阻塞，也就是给定的所有key都不存在或者包含空列表，那么blpop命令将阻塞连接，直到等待超时，或另一个客户端对给定的key执行lpush或rpush为止。超时参数timeout接受一个以秒为单位的数字作为值。超时参数设为0表示阻塞时间可以无限期延长。  
> 相同的key可以被多个客户端同时阻塞，不同的客户端放进一个队列里，按先阻塞先服务的顺序为key进行blpop命令。  
> Blpop可以用在pipeline(批量的发送多个命令并读入多个回复)[不是很理解]

**llen** 返回key的长度

**lrange** 返回列表key中指定区间内的元素。 
 
**lset** lset key index value 将列表key下标为index的元素的值改为value

**ltrim** ltrim key start stop 对一个列表进行修剪，让列表只保留指定区间内的元素，不在指定区间内的元素都将被删除。  

**lindex** lindex key index 返回列表key中，下标为index的元素。

**linsert** linsert key before | after pivot value,将value插入到列表key当中，位于值pivot之后，当pivot不存在于列表key时，不执行任何操作。当key不存在时，key被视为空列表，不执行任何操作。如果key不是列表类型，返回一个错误。

**rpoplpush** rpoplpush source destination 一个原子时间内执行两个操作。将列表source中的最后一个元素弹出，返回客户端，将source弹出的元素插入到列表destination，作为destination列表的头元素  

> **一个安全的队列**。redis经常被用作队列，用户在不同程序之间有效的交换信息。一个程序(producer)通过lpush命令将消息放入队列，而另一个程序(consumer)通过rpop取出队列中等待时间最长的消息。  
> 不幸的是，在这个过程中，一个消费者可能在获取一个消息之后崩溃，而未执行完成的消息也会因此丢失。  
> 使用rpoplpush命令可以解决这个问题，因为它在返回一个消息之余，还将该消息添加到另一个列表中，另外的这个列表可以用作消息的备份表:如果一起正常，当消费者完成该消息的处理之后，可以用lrem命令将该罅隙从备份表中删除。
> 另一方面，助手程序可以通过监视备份表，将超过一定处理时限的消息重新放入队列中去(负责处理该消息的消费者可能已经崩溃)，这样就不会丢失任何消息了。  

**BRPOPLPUSH** brpoplpush source destination timeout 是上一个命令的阻塞版本。

## SETS
> redis中的集合，是一种无序的集合，集合中的元素没有先后顺序

集合相关的操作也很丰富，如添加新元素、删除已有元素、取交集、取并集、取差集。  

对于集合的使用，也有一些常见的场景。如QQ的好友标签，就是把每一个用户的标签储存在一个集合之中。

**SADD** sadd key member 将一个或者多个member元素加入到集合key中，已经存在集合的member元素将被忽略。

**SREM** srem key member 移除集中key中的一个或多个member元素，不存在的member元素会被忽略。

**smembers** smembers key 返回集合key中的所有成员。

**sismember** 判断member元素是否是key的成员。

**scard** scard key 返回集合key的基数

**smove** smove source destination member   
将member元素从source集合移动到destination集合。  
如果source集合不存在或不包含指定的member元素，则smove命令不执行任何操作，仅返回0.否则，member元素从source集合中被移除，并添加到destination集合中去。  
当destination集合已经包含member元素时，smove命令只是简单地将source集合中的member元素删除。

**Spop** spop key 移除并返回集合中的一个随机元素。  
返回被移除的随机元素，当key不存在或key是空集时，返回nil

**srandmember** srandmember key 返回集合中的一个随机元素

**sinter** sinter key 返回一个集合的全部成员，该集合是所有给定集合的交集

**sinterstore** sinterstore destination key 它与sinter相同，但它将结果保存到destination集合，而不是简单地返回结果集。如果destination可以是key本身。

**sunion** 返回一个集合的全部成员，该集合时所有给定集合的并集。  

**sunionstore** sunionstore destination key 此命令等同于sunion，但它将保存到destination集合，而不是简单地返回结果集。   
如果destination已经存在，则将其覆盖。destination可以是key本身。

**sdiff** sdiff key 返回一个集合的全部成员，该集合是所有给定集合的差集。

**sdiffstore** 此命令等同于sdiff，但它将结果保存到destination集合中，而不是简单地返回结果集。

## 有序集合
redis不仅提供了无序集合(sets), 还提供了有序集合(sorted sets)。有序集合中的每个元素都关联一个序号(score),这便是排序的一句。

将redis中的有序集合称为zsets，这是因为有序集合相关的操作指令都是以z开头的，比如zrange、zadd、zrevrange、zrangebyscore等

**Zadd**将一个zadd key score member 将一个或多个member元素机器score值加入到有序集key当中

**Zrem**  移除有序集key中的一个或多个成员，不存在的成员将被忽略。

**Zcard** 返回有序集key的基数

**Zcount** 返回有序集key中，score值在min和max之间的成员

**Zscore** 返回有序集key中，成员member的score值

**Zincrby** 为有序集key的成员member的score值加上增量increment。

**Zrange** 返回有序集key中，指定区间内的成员，按score值递增排序

**Zrevrange** 返回有序集中，指定区间内的成员。其中成员的位置按score值递减来排列。具有相同score值的成员按字典序反序排列

**Zrangebyscore** 返回有序集key中，所有score值介于min和max之间的成员。有序集成员按score值递增次序排列

**Zrank** 返回有序集key中成员member的排名。其中有序集成员按score值递增顺序排列

**Zrevrank** 返回有序集key中成员member的排名。其中有序集成员按score值递减排序。

**Zremrangebyrank** 移除有序集key中，指定排名区间内所有成员。

**Zinterstore** 计算给定的一个或多个有序集的交集，其中给定key的数量必须以numkeys参数指定，并将该交集存储到destination。

**Zunionstore** 计算给定的一个或多个有序集的并集，其中给定key的数量必须以numkeys参数指定，并将该并集存储到destination

## Hash
hash存的是字符串和字符串之间的映射，比如一个用户要存储其全名、姓氏、年龄等等，就很适合用哈西。

**Hset** 将哈希表key中的域field的值设为value

**Hsetnx** 将哈希表key中的域field的值设为value，当且仅当域field不存在  

**Hmset** 同时将多个field-value对设置到哈希表key中  

**Hget** 返回哈希表key中给定域field的值

**Hmget** 返回哈希表key中给定的一个或多个域的值

**Hgetall** 返回哈希表中，所有的域和值  

**Hdel** 删除哈希表key中一个或多个域，不存在的域将被忽略

**Hlen** 返回哈希表key中域的数量

**Hexists** 查看哈希表key中，给定域field是否存在

**Hincrby** 为哈希表key中的域field的值加上增量increment

**Hkeys** 返回哈希表key中的所有域

**Hvals** 返回哈希表key中的所有值

## Redis持久化－RDB&AOF
> RDB (Redis DataBase)，简而言之，就是在不同的时间点，将redis存储的数据生成快照并存储到磁盘等介质上。  
> AOF (Append Only File)，就是将redis执行过的所有写指令记录下来,在下次redis重新启动时，只要把这些写指令从前到后再重复执行一遍，就可以实现数据恢复了。[再仔细研究下，这到底是在什么场景下会使用]
> RDB和AOF两种方式可以同时使用，在这种情况下，如果redis重启的话，则会优先采用AOF方式来进行数据恢复，这就是因为AOF方式的数据恢复完整度高。
> 如果没有持久化的需求，则可以关闭RDB和AOF方式，这样的话，redis就变成纯内存数据库。

###RDB
一种快照式的持久化方法。redis在数据持久化的过程中，会先将数据写入到一个临时文件中，待持久化过程都结束了，才会用这个临时文件替换上次持久化好的文件。这种特性，让我们可以随时进行备份。因为快照文件总是完整可用的。

redis会单独fork一个进程来进行持久话，而主进程是不会进行任何IO操作的，这样就确保了redis极高的性能。

如果需要大规模数据的回复，且对数据恢复的完整性不是非常敏感，那么RDB方式比AOF方式更加高效。

**缺点**，即使你每分钟都备份一次，如果redis故障了，仍然会有近1分钟的数据丢失。

###AOF
只允许追加，不允许改写的文件。  
AOF是将执行过的写指令都记录下来，在数据恢复时按照从前到后的顺序再将指令都执行一遍。

通过配置redis.conf中的appendonly yes 就可以打开AOF功能。如果有写操作，redis就会被追加到AOF文件的末尾。

默认的AOF持久化策略是每秒钟fsync一次(fsync是指把缓存中的写指令记录到磁盘中)，因为这种情况下，redis仍然可以保持很好的处理性能，即使redis挂掉，也只会丢失最近1s的数据。

如果遇到磁盘满、inode满或断电的情况，导致日志写入不完整，也咩有关系，redis提供redis-check-aof工具，用来进行日志修复。  

redis有文件重写机制，因为AOF是不停追加的，所以当文件大于某个大小后，redis会启动AOF文件的内容压缩，只保留可恢复数据的最小指令集。  
>举例，假设我们调用了100次incr指令，再aof中就要写100条指令，但这明显是很低效的，完全可以把100条指令合并成一条set指令，这就是重写机制的原理。

在AOF重写时，仍然是采用先写临时文件，全部完成后再替换的流程，所以断电、磁盘满等问题都不会影响AOF文件的可用性。

**AOF的另一个好处** : 情景再现。会有同学在执行redis时，不小心flushall，导致redis内存中的数据全部被清空。只要配置了AOF持久化的方式，且AOF文件还没有被重写，我们就可以用最快的速度暂停redis并编辑aof文件，将最后一行flushall删除，然后重启redis，就可以恢复redis的所有数据到flushall之前的状态。

如果AOF文件写坏了，redis不会冒然加载有问题的aof文件，而是报错退出。这时可以通过以下步骤来修复出错的文件：

1.备份被写坏的AOF文件  
2.运行redis-check-aof -fix进行修复
3.用diff -u来看下两个文件的差异，确认问题点  
4.重启redis，加载修复后的AOF文件。

## Redis事务
事务就是一个完整的动作，要么全部执行，要么什么都不执行。

**Watch** watch key 监视一个或多个key，如果在事务执行之前这些key被其他命令改动，那么事务将被打断。  

**unwatch** 取消watch命令多所有key的监视

**multi** 标记一个事务块的开始。事务块内的多条命令会按照先后顺序被放进一个队列当中，最后又exec命令在一个原子时间内执行。  

**Exec** 执行所有事务块内的命令。如果某个key正处于watch状态下，且事务块中有和这个key相关的命令，那么exec命令只在这个key没有被其他命令改动的情况下执行并生效，否则事务会被打断。

```
watch lock lock_times
multi
set lock "first"
incr lock lock_times
exec

watch lock lock_times
multi
set lock "second"
incr lock_times
exec
(nil)
```

**discard** 取消事务，放弃执行事务块内的所有命令

在使用Multi组装事务时，每一个命令都会进入到内存队列中缓存起来，如果出现QUEUE，则表示我们这个命令成功插入了缓存队列，在讲嘞执行exec时，这些被queued的命令都会被组装成一个事务来执行。

