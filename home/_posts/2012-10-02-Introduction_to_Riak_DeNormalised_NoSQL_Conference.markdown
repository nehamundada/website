---
layout: post
title: Introduction to Riak at DeNormalised 2012
category: home
date: 03 Oct 2012
summary: Riak was one of the technologies on show at the DeNormalised conference last week. Riak is a distributed key value database that is built to work at huge scale.
---
<a href="http://basho.com/products/riak-overview/"><img src="/asset/images/riaklogo.png" title="Riak" style="float:left;padding-right:10px;"/></a>

[Casey Rosenthal](http://twitter.com/caseyrosenthal) of [Basho](http://basho.com) gave an introduction to [Riak](http://basho.com/products/riak-overview/) on the first day of the [DeNormalised conference](http://skillsmatter.com/event/java-jee/denormalised-nosql-roadshow-2012) and ran one of the hands on training sessions on the second day.

Apart from doing a great job of introducing Riak, Casey won the quote of the conference competition, if there was such a thing. 

> "Riak scales so well I couldn't fit it on the slide"

## Quick intro to Riak

Here are some basic concepts to keep in mind when considering whether to use Riak.

### Riak is a masterless cluster

This is the key to Riak's claim to being a highly scalable distributed database. You can make a request of any node in the cluster and you will get the same response. You can write data to any node in the cluster and the data will be persisted. This is a very different concept to the databases that most people are used to working with such as MySQL, Oracle or even other NoSQL offerings such as MongoDB or Neo4j. All the aforementioned databases have a master node. The most common pattern being that you can read from any node, but only write to the master.

### Riak terminology

The statements below don't cover everything but are some key terms that you need to understand within the context of Riak.

A *cluster* is made up of many *nodes*.

A *cluster* can have many *buckets* of *objects*.

*Buckets* are namespaces for *objects*.

*Objects* have a *key*, a *value* and *metadata*.

A *key* is a *String*

A *value* can be anything

You can check out the [Riak Glossary](http://wiki.basho.com/Riak-Glossary.html) for a more in-depth explanaition of these terms.

### Data distribution, hashing and the ring

Each node has a number of [vNodes](http://wiki.basho.com/Riak-Glossary.html#Vnode), or "Virtual Nodes", which are used for the purpose of distributing data across the cluster. Riak uses a hashing algorithm to determine which vNode should store the data for an object. The hash function takes the bucket name and key name as inputs and produces an integer from 0 to 2^160. Each vNode owns a range of numbers from 0 to 2^160. The distribution of these vNodes is known as the ring. Below is a simplified diagram showing how vNodes are distributed across the hash function output number range and also across nodes.

![Riak vNode Distribution](/asset/images/ring_ish.png)

N.B. The default number of vNodes is 32. I've just shown 16 vNodes distributed over 4 nodes to simplify the concept.

![Writing an object to Riak](/asset/images/ring_writing_to.png)

Once the hash value of the object has been calculated then data is sent to the vNode that manages the range which the number lives within. The data is then written to the next two (assuming the default replication value of 3) vNodes in the sequence. This ensures the data is replicated over a number of physical machines due to the distribution of vNodes over physical machines.


## Querying Riak

You can get data out of Riak in a number of ways:
* Query by key (it's a key value store, so I guess you would expect this!)
* Query by secondary index
* Full-text search
* MapReduce

### Querying Riak by key

The primary method of getting data out of Riak is to query by key. This is the fastest and most efficient method, as the same hashing algorithm can be used to find the node on which the data lives and then return the data to the client. Here's some example Java code adding a new object and fetching it by key.

<pre>
@Test
public void putSomethingInRiak() throws RiakException {
    PBClientConfig pbConfig = new PBClientConfig.Builder()
            .withHost("127.0.0.1")
            .withPort(8081)
            .build();

    IRiakClient riakClient = RiakFactory.newClient(pbConfig);
    bucket = riakClient.fetchBucket("denormalised").execute();
    bucket.store("key1", "value1").execute();

    IRiakObject myData = bucket.fetch("key1").execute();

    assertThat(myData.getValueAsString(), is("value1"));
}
</pre>

### Querying Riak by secondary index

Riak implements secondary indexes to allow clients to fetch data using a wider selection criteria than just a key. Secondary indexes are stored in metadata associated with the object. This data is passed to Riak as HTTP headers in the request. Objects with secondary indexes can then be queried by those index key-value pairs. See the Java example below:

<pre>

@Test
public void secondaryIndexTest() throws IOException, RiakException {
    PBClientConfig pbConfig = new PBClientConfig.Builder()
            .withHost("127.0.0.1")
            .withPort(8081)
            .build();

    IRiakClient riakClient = RiakFactory.newClient(pbConfig);
    bucket = riakClient.fetchBucket("denormalised").execute();

    assertTrue(RiakTestProperties.is2iEnabled());

    IRiakObject blue = RiakObjectBuilder.newBuilder("denormalised", "key1")
            .withValue("Has a secondary index [blue]")
            .addIndex("colour", "blue")
            .build();

    IRiakObject red = RiakObjectBuilder.newBuilder("denormalised", "key2")
            .withValue("Has another secondary index [red]")
            .addIndex("colour", "red")
            .build();

    bucket.store(blue).execute();
    bucket.store(red).execute();

    List&lt;String&gt; results = bucket.fetchIndex(BinIndex.named("colour")).withValue("blue").execute();
    assertThat(results.size(), is(1));

    IRiakObject resultLookup = bucket.fetch(results.get(0)).execute();
    assertThat(resultLookup.getValueAsString().contains("[blue]"), is(true));
}

</pre>

While secondary indexes provide more querying flexibility they must be used with care considering the following limitations:

* If your cluster is large (ring size > 512 partitions), performance can be poor
* All results are always returned, so pagination is not supported
* Results are not ordered, clients must take responsibility for this
* Composite queries are not supported


## Why it's interesting

Riak looks like a fascinating product. The highlights for me are:

* masterless & distributed - allows for the very real possibility of zero downtime
* simple scaling - add and remove physical nodes at will and the database rebalances data all while continuing to serve requests

As with most technologies though, you've got to have a good reason to start using it. So when might you consider Riak? If the answer is yes to a number of the following questions then it's worth looking into Riak a bit further:

* Is your data key / value shaped?
* Do you need to process a large number of concurrent read / write transactions?
* Will even a small amount of downtime cost your business a large amount of money?
* Do you need the ability to easily scale your database servers up and down?

I've just scratched the surface of Riak here. A distributed database that scales by simply adding more nodes is not without it's complexities. As Casey said in his session, 'Distributed computing is hard, that's official Basho company policy'. For some problems the hard work and hard thinking is worth it, for many (probably more than would care to admit it) it is not. 

When putting new technologies, such as Riak into use, make sure you test everything early. There is nothing better than putting together a walking skeleton, test driving the features, then load testing early and continuously to make sure the system stands up to your design assumptions. 

### A perfect use case

I'll leave you with one proven use case where we know Riak makes sense. Looking at the above criteria, a great usecase for Riak would be a shopping cart service for an exceptionally popular eCommerce site. Imagine millions of users adding products to, and removing products from, their basket, which is probably keyed on their user ID or a basket ID. A popular eCommerce site must handle many many thousands of updates a second. If the shopping basket is unavailable then your company starts losing money. People can't buy anything if they can't add products to their basket. Also you have seasonal peaks, the busiest online shopping period is November and December, so being able to scale up the servers to deal with more requests is a must. 

It should come as no suprise then that the inspiration for Riak was the [Dynamo white paper](http://www.read.seas.harvard.edu/~kohler/class/cs239-w08/decandia07dynamo.pdf). A white paper describing a highly available key-value store developed by the worlds largest eCommerce site, Amazon.

***

Post by [Jon Dickinson.](http://twitter.com/jonmdickinson)

Jon is the Director of Innovation at Equal Experts. He loves getting his hands on new technologies and finding out what interesting things everyone else at EE is getting up to. He runs the [DeNormalised meetup group](http://www.meetup.com/DeNormalised-London/) as a way of keeping an eye on what is happening in the evolving world of database technologies.

