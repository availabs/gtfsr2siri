#!/usr/bin/env node

'use strict';

var metadataBuilder = require('./metadataBuilder'),
    msg             = require('./sample-bus-vehicle-monitoring-message');


console.log(metadataBuilder(msg));


