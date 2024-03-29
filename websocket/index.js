const WebSocket = require("ws");

const wss = new WebSocket.Server({ port:8032 });

wss.on("connection", ws => {
	console.log("New client connected!");

	ws.on("close", () => {
		console.log("Client has disconnected!");
	})
});