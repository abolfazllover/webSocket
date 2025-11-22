import express from "express";
import { createClient } from "redis";
import { createServer } from "http";
import { Server } from "socket.io";
import { createAdapter } from "@socket.io/redis-adapter";

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
            const token = socket.handshake.auth.token;

            console.log("User connected:", token, socket.id);

            // ذخیره در Hash ها برای O(1) lookup
            await this.db.hSet("users", token, socket.id);
            await this.db.hSet("sockets", socket.id, token);

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
                await this.db.hDel("users", token);
                await this.db.hDel("sockets", socket.id);
            });
        });
    }

    // ------------------------------ Utility ------------------------------

    /**
     * Get socketId from token
     */
    async getSocketIdFromToken(token) {
        return this.db.hGet("users", token);
    }

    /**
     * Get token from socketId (optimized O(1))
     */
    async getTokenFromSocketId(socketId) {
        return this.db.hGet("sockets", socketId);
    }


    // ------------------------------ Listen ------------------------------
    listen() {
        this.httpServer.listen(this.port, () =>
            console.log(`WebSocket server running on port ${this.port}`)
        );
    }
}
