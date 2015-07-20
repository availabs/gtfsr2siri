#!/usr/bin/env node

'use strict';

var metadataBuilder = require('./metadataBuilder'),
    msg             = require('./sample-bus-stop-monitoring-message');


console.log(metadataBuilder(msg));


