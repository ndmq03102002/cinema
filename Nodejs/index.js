const server = require('./helpers/server');
const {io} = require('./helpers/socket');
const redis = require('./helpers/redis');
const constants = require('./lib/constants');
const {handleCancelBooking} = require('./services/cancel');

/**
 * handle update seats
 */
redis.subscribe('booking-channel');
redis.on('message', function (channel, message) {
    message = JSON.parse(message);
    io.sockets.in(message.data.room).emit(channel + ':' + message.event, message.data);
});

io.on('connection', function (socket) {
    socket.on('joinRoom', function (room) {
        socket.join(room);
    });

    socket.on('leaveRoom', function (room) {
        socket.leave(room);
    });
});

redis.on('disconnect', function (socket) {
    socket.leave(room);
});
//end handle update seats

// subscribe key event
redis.psubscribe("__keyevent@0__:expired");

//handle expired key events
redis.on('pmessage', async (pattern, channel, message) => {

    try {
        const result = await handleCancelBooking(message);
        console.log("Cancellation result:", result);
    } catch (error) {
        console.error("Cancellation failed:", error);
    }
});
//end handle cancel ticket

//run server with port
server.listen(constants.SOCKET_IP_PORT, function () {
    console.log('Server is running on port', constants.SOCKET_IP_PORT);
});
