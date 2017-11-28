$(document).ready(function(){
    var webSocket = WS.connect("ws://127.0.0.1:8888");

    webSocket.on("socket/connect", function(session){
        console.log("Successfully Connected!");

        session.subscribe("chat/channel", function(uri, payload){
            console.log("Received message", payload.msg);
        });

        session.publish("chat/channel", {msg: "This is a message!"});
    })

    webSocket.on("socket/disconnect", function(error){
        console.log("Disconnected for " + error.reason + " with code " + error.code);
    })
});
