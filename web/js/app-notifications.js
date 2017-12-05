$(document).ready(function(){
    var conn = new WebSocket('ws://localhost:8888/notification');

    conn.onopen = function(e) {
        console.log("Connection with notifications system established!");
    };
});
