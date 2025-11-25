<!DOCTYPE html>
<html lang="fa" style="height: 100%">
<head>
    <meta charset="UTF-8">
    <title>چت تستی Socket.IO</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('meta')
    <style>
        .bd_item_user{
            cursor: pointer;
        }

        .bd_item_user:hover{
            opacity: 0.7;
        }

        .m_me{
            background: #f1f1f1;
        }
        .m_{
            background: #c8e1e8;
        }
        .m_me,.m_{
            margin: 5px 0;
            display: inline-block;
            padding: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body dir="rtl" style="height: 100%">

<div class="ui container" style="height: 100%">
    <div class="ui grid  middle aligned" style="height: 100%">



        <div class="twelve wide computer column" style="border: 1px solid #ccc;height: 600px;">
            <h4>سلام کاربر گرامی
                {{$user->email}}
            </h4>
            <div class="ui segment" id="headerChat">
                <h4 class="ui blue header">
                    <i class="user grey icon"></i>
                    <div class="content">
                        <span> {{$user->name}}</span>
                        <div class="sub header">{{$user->email}}</div>
                    </div>
                </h4>
            </div>

            <div class="bd_messages" style="height: 430px;overflow-y: auto">
{{--                <div><span class="m_me">salam</span></div>--}}
{{--               <div dir="ltr"><span class="m_">salam bar to</span></div>--}}
            </div>

            <div class="ui form">
                <div class="fields">
                    <div  class="twelve wide field"><input id="inp_message"></div>
                    <div class="four wide field"><button onclick="sendMessage()" class="ui fluid primary button">ارسال</button></div>
                </div>
            </div>
        </div>


        <div class="four wide computer column" style="border: 1px solid #ccc;background: #f7f9ff;height: 600px;overflow-y: auto;border-radius: 10px 0 0 10px;padding: 20px">
           <h3>لیست کاربران</h3>
            <div class="ui divider"></div>
            @foreach($users as $us)
                <h4 data-id="{{$us->id}}" onclick="changeChat({{$us->id}},'{{$us->email}}','{{$us->name}}')" class="bd_item_user ui blue header">
                    <i class="user grey icon"></i>
                    <div class="content">
                       <span> {{$us->name}}</span>
                    <div class="sub header">
                        {{$us->email}}
                    <span class="ui green mini label status">@if($us->is_online) آنلاین @else @endif</span>
                    </div>
                    </div>
                </h4>
                <div class="ui divider"></div>
            @endforeach
        </div>


    </div>
</div>


<script>
    var userIdSelected="{{$users[0]['id']}}";
    const myID="{{$user->id}}";

    const socket=io('http://127.0.0.1:3000',{
        auth : {
            token : '{{$jwtToken}}',
        }
    });

    socket.on("connect", () => {
        console.log("اتصال برقرار شد:", socket.id);
    });

    function changeChat(userId, email, name) {
        userIdSelected=userId;
        socket.emit('changeChat',JSON.stringify({'userID' : userId}))
    }


    function sendMessage() {
        var message=$('#inp_message').val();
        var data={'id' : myID, 'to' : userIdSelected,'message' : message}
        ajax_sender("{{route('sendMessage')}}",data,'post',function (a) {
            console.log(a)
        })
        $('#inp_message').val('');
    }

    function generateItemMessage(msg,isMe){
        if(isMe){
            $('.bd_messages').append(' <div><span class="m_me">'+msg+'</span></div>')
        }else {
            $('.bd_messages').append('<div dir="ltr"><span class="m_">'+msg+'</span></div>')
        }
    }


    socket.on("new_message", (data) => {
        console.log("پیام دریافت شد:", data);
        generateItemMessage(data.message,data.from_id==myID);
    });
    socket.on('update_messages',(messages)=>{
        $('.bd_messages *').remove();
       messages.forEach(function (a) {
           generateItemMessage(a.message,a.from_id==myID)
       })
    })

    socket.on("change_status", (data) => {

        console.log(data);
        console.log(data.is_online);
        if(data.is_online){
            $(`.bd_item_user[data-id=${data.id}]`).find('.status').show()
        }else {
            $(`.bd_item_user[data-id=${data.id}]`).find('.status').hide()
        }
    });



</script>
</body>
</html>
