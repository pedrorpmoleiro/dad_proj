const SSL_CRT = "";
const SSL_PEM = "";

let httpServer;
if (SSL_CRT !== "" && SSL_PEM !== "") {
    console.log("Found SSL Files Path");
    const fs = require("fs");
    let options;
    try {
        options = {
            key: fs.readFileSync(SSL_PEM),
            cert: fs.readFileSync(SSL_CRT),
        };
        console.log("Loaded SSL Certificate");

        httpServer = require("https").createServer(options);
        console.log("HTTPS server enabled");
    } catch (exception) {
        httpServer = require("http").createServer();
    }
} else httpServer = require("http").createServer();

let io = require("socket.io")(httpServer);

const SessionManager = require("./SessionManager");
let sessionManager = new SessionManager();

httpServer.listen(8080, () => console.log("Listening on port 8080"));

io.on("connection", (socket) => {
    console.log("Client has connected. Socket ID = " + socket.id);

    socket.on("disconnect", () => {
        sessionManager.removeSocketIDSession(socket.id);

        console.log("Disconnect  Socket ID= " + socket.id);
        console.log("  -> Total Sessions= " + sessionManager.users.size);
    });

    socket.on("user_logged_in", (user) => {
        if (user && user.id && user.type) {
            sessionManager.addUserSession(user, socket.id);
            socket.join(user.type);
            console.log(
                "User Logged In: UserID= " +
                    user.id +
                    ", Socket ID= " +
                    socket.id
            );
            console.log("  -> Total Sessions= " + sessionManager.users.size);
        }
    });

    socket.on("user_logged_out", (user) => {
        if (user && user.id && user.type) {
            socket.leave(user.type);
            sessionManager.removeUserSession(user.id);
            console.log(
                "User Logged Out: UserID= " +
                    user.id +
                    ", Socket ID= " +
                    socket.id
            );
            console.log("  -> Total Sessions= " + sessionManager.users.size);
        }
    });

    // US 15
    socket.on("order_picked_up", (user) => {
        if (user && user.id && user.type.toUpperCase() === "ED") {
            console.log(
                "Delivery Man Picked Up Order: UserID= " +
                    user.id +
                    ", SocketID= " +
                    socket.id
            );

            const userSessionsByType = sessionManager.getUserSessionsByType(
                "ED"
            );

            if (userSessionsByType.length > 0)
                for (let userSession of userSessionsByType)
                    if (userSession.user.id !== user.id) {
                        io.to(userSession.socketID).emit("order_picked_up");
                        console.log(
                            "Sending Message 'order_picked_up' to UserID= " +
                                userSession.user.id +
                                ", SocketID= " +
                                userSession.socketID
                        );
                    }
        }
    });

    // US 19 - New Order
    socket.on("order_created", (user) => {
        if (user && user.id && user.type.toUpperCase() === "C") {
            console.log(
                "Customer has Created an Order: UserID= " +
                    user.id +
                    ", SocketID= " +
                    socket.id
            );

            const userSessionsByType = sessionManager.getUserSessionsByType(
                "EM"
            );

            if (userSessionsByType.length > 0)
                for (let userSession of userSessionsByType)
                    if (userSession.user.id !== user.id) {
                        io.to(userSession.socketID).emit("order_created");
                        console.log(
                            "Sending Message 'order_created' to UserID= " +
                                userSession.user.id +
                                ", SocketID= " +
                                userSession.socketID
                        );
                    }
        }
    });

    // US 19 - Order Updated
    socket.on("order_updated", (payload) => {
        if (
            payload.user &&
            payload.user.id &&
            (payload.user.type.toUpperCase() === "ED" ||
                payload.user.type.toUpperCase() === "EC") &&
            payload.order &&
            payload.order.customerID
        ) {
            console.log(
                "An Order has been updated by: UserID= " +
                    payload.user.id +
                    ", UserType= " +
                    payload.user.type +
                    ", SocketID= " +
                    socket.id
            );

            const customerSession = sessionManager.getUserSession(
                payload.order.customerID
            );
            if (customerSession) {
                io.to(customerSession.socketID).emit("order_updated");
                console.log(
                    "Sending Message 'order_updated' to UserID= " +
                        customerSession.user.id +
                        ", SocketID= " +
                        customerSession.socketID
                );
            }

            const userSessionsByType = sessionManager.getUserSessionsByType(
                "EM"
            );

            if (userSessionsByType.length > 0)
                for (let userSession of userSessionsByType)
                    if (userSession.user.id !== user.id) {
                        io.to(userSession.socketID).emit("order_updated");
                        console.log(
                            "Sending Message 'order_updated' to UserID= " +
                                userSession.user.id +
                                ", SocketID= " +
                                userSession.socketID
                        );
                    }
        }
    });

    // US 20
    socket.on("order_canceled", (payload) => {
        if (
            payload.user &&
            payload.user.id &&
            payload.user.type.toUpperCase() === "EM" &&
            payload.order &&
            payload.order.status
        ) {
            console.log(
                "An Order has been cancelled: UserID= " +
                    payload.user.id +
                    ", UserType= " +
                    payload.user.type +
                    ", SocketID= " +
                    socket.id
            );

            if (
                payload.order.status.toUpperCase() === "P" &&
                payload.order.cookId
            ) {
                const userSession = sessionManager.getUserSession(
                    payload.order.cookId
                );
                if (userSession) {
                    io.to(userSession.socketID).emit("order_canceled");
                    console.log(
                        "Sending Message 'order_canceled' to UserID= " +
                            userSession.user.id +
                            ", SocketID= " +
                            userSession.socketID
                    );
                }
            } else if (
                payload.order.status.toUpperCase() === "T" &&
                payload.order.deliveryManId
            ) {
                const userSession = sessionManager.getUserSession(
                    payload.order.deliveryManId
                );
                if (userSession) {
                    io.to(userSession.socketID).emit("order_canceled");
                    console.log(
                        "Sending Message 'order_canceled' to UserID= " +
                            userSession.user.id +
                            ", SocketID= " +
                            userSession.socketID
                    );
                }
            } else if (payload.order.status.toUpperCase() === "R") {
                const userSessionsByType = sessionManager.getUserSessionsByType(
                    "ED"
                );

                if (userSessionsByType.length > 0)
                    for (let userSession of userSessionsByType)
                        if (userSession.user.id !== user.id) {
                            io.to(userSession.socketID).emit("order_canceled");
                            console.log(
                                "Sending Message 'order_canceled' to UserID= " +
                                    userSession.user.id +
                                    ", SocketID= " +
                                    userSession.socketID
                            );
                        }
            }
        }
    });

    // US 12
    socket.on("assign_order", (payload) => {
        if (
            payload.cookID &&
            payload.user &&
            payload.user.id &&
            payload.user.type
        ) {
            const userSession = sessionManager.getUserSession(payload.cookID);
            if (userSession) {
                io.to(userSession.socketID).emit("order_assigned");
                console.log(
                    "Sending Message 'order_assigned' to UserID= " +
                        userSession.user.id +
                        ", SocketID= " +
                        userSession.socketID
                );
            }
        }
    });

    // EXTRA
    socket.on("global_message", (payload) => {
        if (
            payload.user &&
            payload.user.id &&
            payload.user.type &&
            payload.user.type === "EM" &&
            payload.message &&
            payload.color
        ) {
            socket.broadcast.emit("global_message", {
                message: payload.message,
                color: payload.color,
            });
            console.log(
                "Broadcast 'global_message' from UserID= " +
                    payload.user.id +
                    ", SocketID= " +
                    payload.socketID
            );
        }
    });
});
