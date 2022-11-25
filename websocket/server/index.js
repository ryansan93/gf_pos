const WebSocket = require("ws");

const wss = new WebSocket.Server({ port:8033, address: '103.137.111.6' });

wss.on("connection", function(socket) {
	console.log("New client connected!");

	socket.on("message", (msg) => {
		wss.clients.forEach(function (client) {
			console.log( JSON.stringify(JSON.parse(msg)) );

			client.send(JSON.parse(msg));
		})
	})

	// socket.on("close", () => {
	// 	console.log("Client has disconnected!");
	// })
});