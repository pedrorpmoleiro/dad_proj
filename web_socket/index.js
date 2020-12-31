let httpServer = require("http").createServer();
let io = require("socket.io")(httpServer);

httpServer.listen(8080, () => console.log("Listening on port 8080"));
io.on("connection", (socket) => {
    console.log("Client has connected. Socket ID = " + socket.id);

    socket.on("disconnect", () => {
        console.log("Client Disconnected. Socket ID = " + socket.id);
    });
});
