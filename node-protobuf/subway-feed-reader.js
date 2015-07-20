#!/usr/bin/env node

'use strict';

// Heavily Based on http://stackoverflow.com/a/25733922 

var ProtoBuf = require('protobufjs'),
    http     = require("http"),
    key      = require('./SUBWAY-API-KEY'),
    _        = require('lodash');


// create a protobuf decoder
var transit = ProtoBuf.protoFromFile('nyct-subway.proto').build('transit_realtime');

var feedUrl = "http://datamine.mta.info/mta_esi.php?key=" + key;    

// HTTP GET the binary feed
http.get(feedUrl, parse);

// process the feed
function parse(res) {
    // gather the data chunks into a list
    var data = [];
    res.on("data", function(chunk) {
        data.push(chunk);
    });
    res.on("end", function() {
        // merge the data to one buffer, since it's in a list
        data = Buffer.concat(data);
        
        // create a FeedMessage object by decooding the data with the protobuf object
        var msg = transit.FeedMessage.decode(data);

        // do whatever with the object
        console.log(JSON.stringify(msg, null, 4));
    }); 
};
