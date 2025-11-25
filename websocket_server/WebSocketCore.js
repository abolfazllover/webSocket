import express from "express";
import { createClient } from "redis";
import { createServer } from "http";
import { Server } from "socket.io";
import { createAdapter } from "@socket.io/redis-adapter";
import jwt from "jsonwebtoken";

export default class WebSocketCoreOptimized {

    constructor(config = {}) {
        this.port = config.port || 3000;
        this.redisUrl = config.redisUrl || "redis://127.0.0.1:6379";

        this.app = express();
        this.httpServer = createServer(this.app);

        this.io = new Server(this.httpServer, {
            cors: config.cors || { origin: "*" }
        });

        this.pubClient = null;
        this.subClient = null;
        this.db = null;
        this.dbSub = null;

        // event handlers
        this.socketHandlers = {};
        this.redisHandlers = {};
    }

    // ------------------------------ Dynamic Handlers ------------------------------
    onSocketEvent(event, callback) {
        this.socketHandlers[event] = callback;
    }

    onRedisEvent(channel, callback) {
        this.redisHandlers[channel] = callback;
    }

    // ------------------------------ Init ------------------------------
    async init() {
        await this.initRedis();
        await this.initSocketIO();
        await this.initRedisSubscribers();
        this.listen();
    }

    async initRedis() {
        this.pubClient = createClient({ url: this.redisUrl });
        this.subClient = this.pubClient.duplicate();

        await this.pubClient.connect();
        await this.subClient.connect();

        this.db = createClient({ url: this.redisUrl });
        await this.db.connect();

        this.dbSub = this.db.duplicate();
        await this.dbSub.connect();
    }

    async initRedisSubscribers() {
        for (const channel in this.redisHandlers) {
            await this.dbSub.subscribe(channel, async data => {
                this.redisHandlers[channel](JSON.parse(data), this);
            });
        }
    }

    async initSocketIO() {
        this.io.adapter(createAdapter(this.pubClient, this.subClient));

        this.io.on("connection", async socket => {
            const JWTtoken = socket.handshake.auth.token;


            if (!JWTtoken) {
                console.log("⛔ Token not provided");
                socket.disconnect();
                return;
            }

            let decoded = null;

            try {
                decoded = jwt.verify(JWTtoken, 'o9MYQoIeusn00kj9OyKSOC8jP8UmK6HFn4DWhE8Phq7krkS01R5TxNxHeOJTiAuI'); // ← همون کلیدی که لاراول استفاده می‌کنه
            } catch (err) {
                console.log("⛔ Invalid token:", err.message);
                socket.disconnect();
                return;
            }

            // ذخیره در Hash ها برای O(1) lookup
            await this.db.hSet("users", decoded.sub, socket.id);
            await this.db.hSet("sockets", socket.id, decoded.id);

            if (this.socketHandlers["connection"]) {
                this.socketHandlers["connection"](socket, this);
            }

            // ثبت بقیه رویدادها
            for (const event in this.socketHandlers) {
                if (event !== "connection") {
                    socket.on(event, (data) => {
                        this.socketHandlers[event](socket, data, this);
                    });
                }
            }

            socket.on("disconnect", async () => {
                if (this.socketHandlers["disconnect"]) {
                    this.socketHandlers["disconnect"](socket, this);
                }
                await this.db.hDel("users", decoded.sub);
                await this.db.hDel("sockets", socket.id);
            });
        });
    }

    // ------------------------------ Utility ------------------------------

    /**
     * Get socketId from token
     */
    async getSocketIdFromID(id) {
        return this.db.hGet("users", id);
    }

    /**
     * Get token from socketId (optimized O(1))
     */
    async getIDFromSocketId(socketId) {
        return this.db.hGet("sockets", socketId);
    }


    async messageToServer(socket,action,msg){
        msg=JSON.parse(msg);
        msg['action']=action;
        msg['fromID']=await this.getIDFromSocketId(socket.id);
        return this.db.publish('server_message',JSON.stringify(msg))
    }


    // ------------------------------ Listen ------------------------------
    listen() {
        this.httpServer.listen(this.port, () =>
            console.log(`WebSocket server running on port ${this.port}`)
        );
    }
}
