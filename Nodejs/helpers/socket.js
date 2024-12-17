const socketIO = require("socket.io");
const server = require('./server');
const constants = require('../lib/constants');


const io = socketIO(server, {
    cors: {
        origin: constants.SOCKET_IP_HOST, methods: ["GET", "POST"], credentials: true,
    }, allowEIO3: true
});

module.exports = {io};
