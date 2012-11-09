---
layout: post
title: Evolutionary design in practice
category: home
date: 12 Feb 2012
summary: A common practice on software development projects is to start with a lengthy tool choice process to make sure the right technologies are employed for the requirements of the project. In practice we find this process can be time consuming and often leads teams down the wrong path as they consider requirements that might materialise in the future. Instead we prefer to pick a set of tools that the team is familiar with that is likely to do the job, and then adapt as requirements become concrete.
---
A common practice on most software development projects is to start with a lengthy tool choice process to make sure the right technologies are employed for the requirements of the project. In practice we find this process can be time consuming and often leads teams down the wrong path as they consider requirements that might materialise in the future. Instead we prefer to pick a set of tools that the team is familiar with that is likely to do the job, and then adapt as requirements become concrete.

**The Project**

We recently had a concrete example of this approach working on a project to produce an online, agent specific, mobile phone shop that interacts with an existing JSON REST API for product information, a basket API and an ordering service. The shop is expected to have a rich UI with multiple widgets on the page, interacting with the back end services to populate a basket with the items being purchased, then checked out.

**The Initial Architecture**

We chose to have a light java server application, using [Jersey](http://jersey.java.net/), and a [CoffeeScript](http://coffeescript.org/)/HTML front end. We wanted to just proxy calls through the server, to the existing REST apis, leaving the majority of the code in CoffeeScript. Because [JSON](http://json.org/) is very easy to consume and produce within CoffeeScript, we decided that we really didn't need a separate server side component to process or manipulate the the JSON being returned from the existing apis. From previous experience, we had found that manipulating JSON in Java was an overly verbose and tedious process, so we expected significant productivity gains from our approach.

In the very early stages of the project, this approach worked pretty well. The tools for writing TDD CoffeeScript were working nicely, and the app took shape quickly. There were a few complications where we needed to make several calls to get all the data for a single UI component, or we had to traverse a number of links in the REST API before we got to the resource we needed to post to. This made the CoffeeScript messy, but we separated out the model and view components into separate classes to make it clearer.

**The Point of Failure**

As the complexity of the CoffeScript code grew, and the number of different views of the same data increased, we started to struggle with keeping a track of what shape of data was being passed to which component, and bugs started creeping in. With the need to walk a number of links to complete actions like adding items to a basket, the setup and mocks in the tests were getting out of hand.

It was mostly the complex tests that were hurting us, but this is a common symptom of overly complex code with poor separation of concerns. In order to simplify things, we decided we really needed controllers to call the various REST APIs, walk the links and return data that was the right shape for the widgets on the page. After much discussion, we decided that ideally we would like to handle this data manipulation server side, thereby making the server code more than just a proxy. The problem we had to overcome was how to make the server side code simple. Having used Java to manipulate JSON before, we weren't keen to repeat the experience. Ideally we wanted the JSON to be marshalled into strongly typed objects rather than lists and maps. This would remove any confusion over the shape of data a particular component was dealing with.

**The Architectural Shift**

Having a few members of the team that had used [Scala](http://scala-lang.org/) before, we decided to look into using Scala for our server. After some investigation, we discovered that with a few tweaks, we could use the [Jerkson](http://github.com/codahale/jerkson) JSON library, which follows the Scala [Pimp my library approach](http://artima.com/weblogs/viewpost.jsp?thread=179766) of taking a good existing Java library [Jackson](http://jackson.codehaus.org/), and making it more Scala friendly. We could then create case classes for each of our JSON responses, which are very terse but clear. The nice addition of having to declare nullable fields as Options is a great feature, making the whole null checking problem much easier. Couple that with Scala's collections APIs which make filtering, sorting and iterating over collections very concise, and the data manipulation became clear and simple to follow.

With the controller being server side, we achieved a one to one data model to widget relationship. The CoffeeScript classes were able to be significantly simplified, and the tests became much easier and clearer. While the project isn't finished yet, we are much more confident, and are now able to develop new functionality faster.

Post by [Chris Tarttelin.](http://twitter.com/pyruby)

Chris has been a developer for over 15 years. He specialises in identifying the real business value, and providing simple solutions to deliver that value. "I'm a geek and just want to keep writing great, simple software to solve big complex problems". He is one of the many associates that works with Equal Experts to help build great software.