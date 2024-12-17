import Echo from 'laravel-echo'

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'ba1422af6dc69eb28c',
    cluster: 'ap1',
    encrypted: true
});
