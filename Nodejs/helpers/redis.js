const Redis = require('ioredis');
const constants = require('../lib/constants');

const redis = new Redis(constants.REDIS_IP_PORT, constants.REDIS_IP_HOST);

module.exports = redis;
