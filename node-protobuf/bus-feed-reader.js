'use strict';

var https = require("https"),
    key   = require('./BUS-API-KEY');


module.exports = function (toMonitor) {

    var feedUrl = 'https://bustime.mta.info/api/siri/' + 
                    toMonitor + '-monitoring.json?key=' + 
                    key + '&OperatorRef=MTA&MonitoringRef=308209&LineRef=MTA%20NYCT_B63';

    https.get(feedUrl, parse);

    function parse(res) {
        var data = [];

        res.on("data", function(chunk) {
            data.push(chunk);
        });

        res.on("end", function() {
            var msg = JSON.parse(data.join(''));
            console.log(JSON.stringify(msg, null, 4));
        }); 
    };
};
