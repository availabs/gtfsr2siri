#!/usr/bin/env node

'use strict';

var http     = require("https"),
    key      = require('./BUS-API-KEY');

var feedUrl = 'https://bustime.mta.info/api/siri/stop-monitoring.json?key=' + key + '&OperatorRef=MTA&MonitoringRef=308209&LineRef=MTA%20NYCT_B63';

http.get(feedUrl, parse);

function parse(res) {
    var data = '';

    res.on("data", function(chunk) {
        data += chunk;
    });

    res.on("end", function() {
        var msg = JSON.parse(data);

        console.log(JSON.stringify(msg, null, 4));
    }); 
};
