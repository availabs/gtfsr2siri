'use strict';

var _        = require('lodash');


function recursiveExaminer (value, name, path, acc) {

    var newPath = path || '',
        keys,
        i;

    acc = acc || [];

    if (name) {
        newPath += (newPath ? ',' : '') + name;
        acc.push(newPath);
    }

    if (Object.prototype.toString.call(value) === '[object Object]') {
        keys = Object.keys(value);
        for (i=0; i<keys.length; ++i) {
            recursiveExaminer(value[keys[i]], keys[i], newPath, acc);
        }
    }

    if (Object.prototype.toString.call(value) === '[object Array]') {
        for (i=0; i<value.length; ++i) {
            recursiveExaminer(value[i], null, newPath, acc);
        } 
    }

    return acc;
}

function metaDataBuilder (jsValue) {
    return _.uniq(recursiveExaminer(jsValue));
}

module.exports = metaDataBuilder;
