// import express from "express";
// import { createClient } from "redis";
// import { createServer } from "http";
// import { Server } from "socket.io";
// import { createAdapter } from "@socket.io/redis-adapter";
//
// const app = express();
// const httpServer = createServer(app);
//
// // 1) Socket.IO Redis adapter clients
// const pubClient = createClient({ url: "redis://127.0.0.1:6379" });
// const subClient = pubClient.duplicate();
//
// await pubClient.connect();
// await subClient.connect();
//
// // 2) Normal Redis for DB operations
// const db = createClient({ url: "redis://127.0.0.1:6379" });
// await db.connect();
//
// // 3) Subscribe for your custom messages (separate sub client)
// const dbSub = db.duplicate();
// await dbSub.connect();
//
// await dbSub.subscribe("new_message", async (data) => {
//     const obj = JSON.parse(data);
//     await new_message(obj);
// });
//
// await dbSub.subscribe("users_status", async (data) => {
//     console.log('users status subed ',data)
// });
//
//
//
//
// // SOCKET.IO
// const io = new Server(httpServer, {
//     cors: { origin: "*" }
// });
//
// io.adapter(createAdapter(pubClient, subClient));
//
// io.on("connection", async (socket) => {
//
//     const token = socket.handshake.auth.token;
//     console.log("User connected", token, socket.id);
//
//     await db.hSet("users", token, socket.id);
//     changeStateUser(token,true)
//
//     socket.on("new_message", new_message);
//
//     socket.on("disconnect", () => {
//         changeStateUser(token,false)
//         db.hDel("users", token);
//     });
// });
//
// // SEND MESSAGE
// async function new_message(msg) {
//
//     const { token_from, token_to,message } = msg;
//
//     const id_to = await db.hGet("users", token_to);
//     const id_from = await db.hGet("users", token_from);
//
//     io.to(id_to).emit("new_message", msg);
//     io.to(id_from).emit("new_message", msg);
// }
//
// async function changeStateUser(userToken,is_online){
//    const result= await db.publish("server_message", JSON.stringify({'action' : 'chaneStateUser',token : userToken,'is_online' : is_online}));
//     console.log('result to server message ',result)
//     var users=await db.hGetAll('users');
//     for (const token in users) {
//         const socketId = users[token];
//         io.to(socketId).emit('change_status',{'userToken': userToken,'is_online' : is_online})
//     }
// }
//
// httpServer.listen(3000, () => console.log("server is run"));


import WebSocketCore from "./WebSocketCore.js";
const ws=new WebSocketCore();

//  for online user
ws.onSocketEvent('connection',async function (socket,server){
    const token=await ws.getTokenFromSocketId(socket.id);
    var result=   await ws.db.publish('server_message',JSON.stringify({'action' : 'chaneStateUser','token' : token,'is_online' : true}));
    console.log('publish server message connection to token : ',token, result)
})

// for offline user
ws.onSocketEvent('disconnect',async function (socket,server){
    const token=await ws.getTokenFromSocketId(socket.id);
    var result=  await ws.db.publish('server_message',JSON.stringify({'action' : 'chaneStateUser','token' : token,'is_online' : false}));
    console.log('publish server message disconnect', result)
})

ws.onRedisEvent('users_status',function (data,server) {
    console.log('users_state ',data);
    server.io.emit('change_status',data)
})

ws.init();
