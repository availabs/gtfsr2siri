#!/usr/bin/env node

'use strict';

var metadataBuilder = require('./metadataBuilder'),
    msg             = require('./sample-subway-message');


console.log(metadataBuilder(msg));


