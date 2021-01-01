class SessionManager {
    constructor() {
        this.users = new Map();
    }

    getUserSession(userID) {
        return this.users.get(userID);
    }

    getUserSessionsByType(userType) {
        let usersType = [];

        for (const i in this.users) {
            const userSession = this.users.get(i);
            console.log(userSession);
            if (userSession.user.type === userType.toUpperCase())
                usersType.push(userSession);
        }

        return usersType;
    }

    addUserSession(user, socketID) {
        // If already exists a session for this user ID, reuse the session
        let userSession = this.getUserSession(user.id);
        if (userSession) {
            userSession.user = user;
            userSession.socketID = socketID;
            return;
        }
        // Otherwise, create a new session
        userSession = {
            user: user,
            socketID: socketID,
        };
        this.users.set(user.id, userSession);
        return userSession;
    }

    removeUserSession(userID) {
        let userSession = this.getUserSession(userID);
        if (!userSession) {
            return null;
        }
        let cloneUserSession = Object.assign({}, userSession);
        this.users.delete(cloneUserSession.user.id);
        return cloneUserSession;
    }

    removeSocketIDSession(sessionID) {
        for (let [userID, userSession] of this.users) {
            if (userSession.socketID === sessionID) {
                let cloneUserSession = Object.assign({}, userSession);
                this.users.delete(cloneUserSession.user.id);
                return cloneUserSession;
            }
        }
        return null;
    }
}

module.exports = SessionManager;
