import WebSocketCore from "./WebSocketCore.js";
const ws=new WebSocketCore();

//  for online user
ws.onSocketEvent('connection',async function (socket,server){
    const userID=await ws.getIDFromSocketId(socket.id);
    await ws.messageToServer(socket,'chaneStateUser', JSON.stringify({'userID': userID, 'is_online': true}));
    console.log('publish server message connection to userID : ',userID)
})

// for offline user
ws.onSocketEvent('disconnect',async function (socket,server){
    const userID=await ws.getIDFromSocketId(socket.id);
    await ws.messageToServer(socket,'chaneStateUser', JSON.stringify({'userID': userID, 'is_online': false}));
    console.log('publish server message disconnect')
})

ws.onSocketEvent('changeChat', async (socket, msg) => ws.messageToServer(socket,'changeChat', msg));

ws.onRedisEvent('users_status',function (data,server) {
    console.log('users_state ',data);
    server.io.emit('change_status',data)
})

ws.onRedisEvent('new_message',async function (data,server) {
    const {message,from_id,to_id} = data;
    const socketIdTo=await ws.getSocketIdFromID(String(to_id));
    const socketIdfrom=await ws.getSocketIdFromID(String(from_id));
    ws.io.to([socketIdTo,socketIdfrom]).emit('new_message',data)
})

ws.onRedisEvent('update_messages',async function(data,server){
    const {userID,messages}=data;
    const socketID=await ws.getSocketIdFromID(userID);
    ws.io.to(socketID).emit('update_messages',messages);
})

ws.init();
