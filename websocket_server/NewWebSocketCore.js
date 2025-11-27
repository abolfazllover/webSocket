import express from "express";
import { createClient } from "redis";
import { createServer } from "http";
import { Server } from "socket.io";
import { createAdapter } from "@socket.io/redis-adapter";
import jwt from "jsonwebtoken";

// Kafka
import { Kafka } from "kafkajs";

export default class WebSocketCoreOptimized {

    constructor(config = {}) {
        this.port = config.port || 3000;

        // Redis
        this.redisUrl = config.redisUrl || "redis://127.0.0.1:6379";

        // Kafka
        this.kafka = new Kafka({
            clientId: config.clientId || "socket-node",
            brokers: config.kafkaBrokers || ["127.0.0.1:9092"]
        });

        this.producer = this.kafka.producer();
        this.consumer = this.kafka.consumer({
            groupId: config.groupId || "socket-cluster-group"
        });

        // Express / Socket.IO
        this.app = express();
        this.httpServer = createServer(this.app);

        this.io = new Server(this.httpServer, {
            cors: config.cors || { origin: "*" }
        });

        // Redis client
        this.db = null;
        this.pubClient = null;
        this.subClient = null;

        // Handlers
        this.socketHandlers = {};
        this.kafkaHandlers = {};
    }

    // ------------------------------
    // Register handlers
    // ------------------------------
    onSocketEvent(event, callback) {
        this.socketHandlers[event] = callback;
    }

    onKafkaEvent(topic, callback) {
        this.kafkaHandlers[topic] = callback;
    }

    // ------------------------------
    async init() {
        await this.initRedis();
        await this.initKafka();
        await this.initSocketIO();
        this.listen();
    }

    // ------------------------------
    // Redis for storage + adapter
    // ------------------------------
    async initRedis() {
        this.db = createClient({ url: this.redisUrl });
        await this.db.connect();

        this.pubClient = createClient({ url: this.redisUrl });
        this.subClient = this.pubClient.duplicate();

        await this.pubClient.connect();
        await this.subClient.connect();
    }

    // ------------------------------
    // Kafka Producer + Consumer
    // ------------------------------
    async initKafka() {
        await this.producer.connect();
        await this.consumer.connect();

        // subscribe to all topics declared via onKafkaEvent()
        for (const topic in this.kafkaHandlers) {
            await this.consumer.subscribe({ topic });
        }

        // listen
        await this.consumer.run({
            eachMessage: async ({ topic, message }) => {
                const data = JSON.parse(message.value.toString());

                console.log('new message from kafka',data);

                // Run developer handler
                if (this.kafkaHandlers[topic]) {
                    await this.kafkaHandlers[topic](data, this);
                }
            }
        });
    }

    // ------------------------------
    // Socket.IO + Redis Adapter
    // ------------------------------
    async initSocketIO() {

        // IMPORTANT: redis adapter for multi-node socket.io
        this.io.adapter(createAdapter(this.pubClient, this.subClient));

        this.io.on("connection", async socket => {
            const token = socket.handshake.auth.token;
            if (!token) return socket.disconnect();

            let decoded;
            try {
                decoded = jwt.verify(token, "o9MYQoIeusn00kj9OyKSOC8jP8UmK6HFn4DWhE8Phq7krkS01R5TxNxHeOJTiAuI");
            } catch {
                return socket.disconnect();
            }

            // store mapping
            await this.db.hSet("users", decoded.sub, socket.id);
            await this.db.hSet("sockets", socket.id, decoded.id);

            // call developer handler
            if (this.socketHandlers["connection"]) {
                this.socketHandlers["connection"](socket, this);
            }

            // other events
            for (const event in this.socketHandlers) {
                if (event !== "connection") {
                    socket.on(event, data => {
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

    // ------------------------------
    // Storage helpers
    // ------------------------------
    async getSocketIdFromID(id) {
        return await this.db.hGet("users", id);
    }

    async getIDFromSocketId(socketId) {
        return await this.db.hGet("sockets", socketId);
    }

    // ------------------------------
    // Send message TO Kafka
    // ------------------------------
    async sendToKafka(topic, action, socket, msg) {
        msg.action = action;
        msg.fromID = await this.getIDFromSocketId(socket.id);

        await this.producer.send({
            topic,
            messages: [{ value: JSON.stringify(msg) }]
        });
    }

    // ------------------------------
    listen() {
        this.httpServer.listen(this.port, () =>
            console.log(`Node started on port ${this.port}`)
        );
    }
}
