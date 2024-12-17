const http = require('http');
const contains = require('../lib/constants')
const handleCancelBooking = async (ticket_id) => {

    const postData = JSON.stringify({
        ticket_id: ticket_id
    });

    const options = {
        hostname: contains.HOST_NAME_BACKEND,
        port: contains.PORT_BACKEND,
        path: '/api/web/ticket/cancel-ticket',
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Content-Length': Buffer.byteLength(postData),
        },
    }

    let data = '';

    return new Promise((resolve, reject) => {

        const request = http.request(options, (response) => {
            response.setEncoding('utf8');

            response.on('data', (chunk) => {
                data += chunk;
            });

            response.on('end', () => {
                resolve(data);
            });
        });

        request.on('error', (error) => {
            reject(error);
        });
        request.write(postData);
        request.end();
    });
}

module.exports = {handleCancelBooking}
