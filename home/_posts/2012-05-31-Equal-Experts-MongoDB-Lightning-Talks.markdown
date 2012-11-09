---
layout: post
title: Equal Experts MongoDB Lightning Talks
category: home
date: 31 May 2012
summary: At the last <a href="http://www.meetup.com/London-MongoDB-User-Group/events/61894262/">MongoDB user group</a> a few our super passionate MongoDB geeks got together to give a series of lightning talks on interesting things they have been doing with MongoDB.
---
![MongoDB](/asset/images/logo_mongo.png)
##Realtime analytics with MongoDB
First up was [Julian Browne](http://twitter.com/julianbrowne). Julian talked about an experiment he is toying with to use [node.js](http://nodejs.org), [D3.js](http://d3js.org) and [MongoDB](http://mongodb.org) for real-time analytics. He started with his inspiration for the experiment when he found [Hummingbird](http://hummingbirdstats.com/) a tool for real time web traffic visualisation. Julian had also been experimenting with various parts of MongoDB for his upcoming talk at the DeNormalised conference [Deep dive into MongoDB](http://denormalised.com/denomormalised_conference_lond/), when he decided to find out what would happen when if he tailed a capped collection in MongoDB to provide the data for a real-time analytics in Node.js.

Julian moved on to explaining capped collections and tailable cursors, showing how to create a MongoDB capped collection and then the code to tail it. Once that was covered we saw the demo running. Julian fired up the MongoDB instance, created the capped collection, then started Node.js and opened the demo page in his browser to open a web socket to the Node server and render the initial D3 chart. Then back to the terminal, and as Julian saved new documents to the collection the D3 chart could be seen automatically updating in the background. Then he fired off a script to run a large number of random updates to the collection and we could watch the chart respond and update in real time.

Julian then went on to explain that the replication op log in MongoDB is also a capped collection so using the same technology stack, but with a different D3 visualisation Julian fired up a replica set and showed how easy it would be to monitor and visualise any updates to your MongoDB database in real time.

All of the code for this talk, including the scripts for automatically setting up a local MongoDB replica set [can be found on github](https://github.com/julianbrowne/rtsdemo).

##Modelling and validating your schema in MongoDB
Next up it was [Chris Tarttelin](http://twitter.com/pyruby) to talk about schema modelling and validation in MongoDB. A couple of years ago Chris was working on a property listing product. They found that the business required new attributes to be added to properties frequently over time. These properties would be extremely important to a small number of properties, but completely irrelevant to the majority. A classic example being distanceFromVolcano, not so important in New York, more so in Hawaii. 

After a while Chris got fed up of telling his customer, "It will take 3 days to add a new column to the database", due to the hoop jumping that was necessary to get a schema change made. While thinking on this problem Chris experienced a moment of enlightenment at PyCon 2010. We have a REST API talking JSON and here is MongoDB that talks JSON. Why not put them together? You'll be pleased to know it all ended well and the rest is history.

One of the main arguments that they came up against with DBAs was how do we control the schema? This seems to be a recurring theme as the same questions came up a year later on a different project. It turns out there is a draft [JSON schema](http://json-schema.org/) specification, although there wasn't a well maintained Java implementation available at the time. So Sean (coming later) and Chris decided to build one. The result is a [JSON schema validator](https://github.com/EqualExperts/json-schema-validator) that is on github and can be used now.

##Agile development with MongoDB
[Sean Reilly](http://twitter.com/seanjreilly) talked about automated testing of applications using MongoDB. He started of by talking about what we test in MongoDB. For example write concerns, unique indexes, documents persisted, queries return the right objects, versioning strategies implemented correctly.

Sean went step by step through the approach that he developed to make automated testing of the parts of the application that use MongoDB simple and efficient. He also covered some approaches that didn't work so well so you can learn from our experience of using this approach for over a year now.

In summary, here are a couple of tips from the end of the presentation to get you started:

### If you use a shared MongoDB server for unit tests
* Use --smallfiles for a unit test server
* Increase file handles for connections

Also Alvin Richards mentioned that you could use --noprealloc to disable data file preallocation, which would help when creating a new database for each test run.

### If everyone has their own server
* CI server also needs to have a Mongo instance.
* If you are using Jenkins, use the MongoDB plugin.

Sean went on to walk through some code examples afterwards to show the patterns in practice. You can [download Sean's presentation](/asset/other/TestingMongoDB.pdf) now.
