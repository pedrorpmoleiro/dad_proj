import HTTP from "http";
import SessionManager from "./SessionManager";
import SocketIO from "socket.io";

let httpServer = HTTP.createServer();
// let io = require("socket.io")(httpServer);
let io = SocketIO(httpServer);
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
        if (user) {
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
        if (user) {
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
        if (user && user.type.toUpperCase() === "ED") {
            const userSessionsByType = sessionManager.getUserSessionsByType(
                "ED"
            );

            for (let userSession of userSessionsByType)
                if (userSession.user.id !== user.id)
                    io.to(userSession.socketID).emit("order_picked_up");
        }
    });

    // US 19 - New Order
    socket.on("order_created", (user) => {
        if (user && user.type.toUpperCase() === "C") {
            const userSessionsByType = sessionManager.getUserSessionsByType(
                "EM"
            );

            for (let userSession of userSessionsByType)
                if (userSession.user.id !== user.id)
                    io.to(userSession.socketID).emit("order_created");
        }
    });

    // US 19 - Order Updated
    socket.on("order_updated", (user) => {
        if (
            user &&
            (user.type.toUpperCase() === "ED" ||
                user.type.toUpperCase() === "EC")
        ) {
            const userSessionsByType = sessionManager.getUserSessionsByType(
                "EM"
            );

            for (let userSession of userSessionsByType)
                if (userSession.user.id !== user.id)
                    io.to(userSession.socketID).emit("order_updated");
        }
    });

    // US 20
    socket.on("order_canceled", (payload) => {
        if (
            payload.user &&
            payload.user.type.toUpperCase() === "EM" &&
            payload.order &&
            payload.order.status
        )
            if (
                payload.order.status.toUpperCase() === "P" &&
                payload.order.cookId
            ) {
                const userSession = sessionManager.getUserSession(
                    payload.order.cookId
                );
                io.to(userSession.socketID).emit("order_canceled");
            } else if (
                payload.order.status.toUpperCase() === "T" &&
                payload.order.deliveryManId
            ) {
                const userSession = sessionManager.getUserSession(
                    payload.order.deliveryManId
                );
                io.to(userSession.socketID).emit("order_canceled");
            } else if (payload.order.status.toUpperCase() === "R") {
                const userSessionsByType = sessionManager.getUserSessionsByType(
                    "ED"
                );

                for (let userSession of userSessionsByType)
                    if (userSession.user.id !== user.id)
                        io.to(userSession.socketID).emit("order_canceled");
            }
    });
});
