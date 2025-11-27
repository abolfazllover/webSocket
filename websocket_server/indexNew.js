import WebSocketCoreOptimized from './NewWebSocketCore.js';
const ws=new WebSocketCoreOptimized(
    {
        kafkaBrokers: ["65.108.27.196:9092"],
        origin: "*",
    }
);

ws.onSocketEvent("connection",async (socket, core) => {
    console.log("new connection :", socket.id);
    await ws.sendToKafka('test-topic','chaneStateUser', socket,{ 'is_online': true});
});

ws.onSocketEvent("disconnect",async (socket, core) => {
    console.log("disconnect socket :", socket.id);
    await ws.sendToKafka('test-topic','chaneStateUser', socket,{ 'is_online': false});
});


ws.onKafkaEvent('node-message',function (data) {
    console.log('data is from kafka : ',data)
})


ws.init();
