function isPersianAndSpecialChars(input) {
    const regex = /^[\u0600-\u06FF\s.!؟]+$/;
    return regex.test(input);
}


function isEqualToLength(input, length) {
    return input.length === length;
}
function isNumeric(input) {
    const regex = /^\d+$/;
    return regex.test(input);
}

function success_alert(msg,title='موفق'){
    $.toast({
        title: title,
        message:msg,
        type: 'success',
    });
}

function error_alert(msg,title='خطا'){
    $.toast({
        title: title,
        message:msg,
        type: 'error',
    });
}

function ajax_sender(url,data,method,success_fun,dataType='json'){
    $.ajax({
       headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       },
       url : url ,
       data : data,
       method : method,
       success : function (e){
           if (dataType==='HTML' || dataType==='html'){
               success_fun(e)
           }else {
               if (e.status==='success'){
                   success_fun(e);
               }else {
                   error_alert(e.msg,'ناموفق')
               }
           }
           $('.loading').prop('disabled',false)
           $('.loading').removeClass('loading')
       },
       dataType : dataType,
       error : function (e){
           console.log(e)
           error_alert('خطا در ارتباط با سرور - با پشتیبان تماس بگیرید.','خطای سرور')
       }

    })
}

function ajax_sender_file(url,file,success_fun){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:url,  // آدرس سرور که فایل باید ارسال شود
        type: 'POST',
        data: file,
        dataType : 'json',
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.status=='error'){
                error_alert(response.msg)
            }else {
                success_fun(response)
            }
        },
        complete : function (){
          $('.loading').removeClass('loading')
        },
        error : function (e){
            console.log(e)
            error_alert('خطا در ارتباط با سرور - با پشتیبان تماس بگیرید.','خطای سرور')
        }
    });
}

function loader_btn(element){
    element.addClass('loading');
    element.attr('disabled',true);
    setTimeout(function (){
        element.removeClass('loading')
        element.attr('disabled',false);
    },8500)
}

async function neshan_api(address,fun){
    var data;
    await $.ajax({
        url : address ,
        headers: {
            'Api-Key': 'service.bb7d204e262845beb20d253b7d7524c5'
        },
        dataType: 'json',
        success : function (e){
            fun(e)
        },
        error : function (e){
            error_alert('خطا در ارتباط با api نشان');
        }
    })
    return data;
}

function random_colors() {
    var colors = [
        '#7e72d3',
        '#d0d372',
        '#72d374',
        '#d372a3',
    ];

    // انتخاب یک رنگ تصادفی از آرایه
    var randomIndex = Math.floor(Math.random() * colors.length);
    return colors[randomIndex];
}

function draw_rout_to_map(map, data, randomColor = false) {
    for (let k = 0; k < data.routes.length; k++) {

        // تعیین رنگ برای مسیر
        var routeColor = '#7e72d3';
        if (randomColor) {
            routeColor = random_colors();
        }

        for (let j = 0; j < data.routes[k].legs.length; j++) {

            for (let i = 0; i < data.routes[k].legs[j].steps.length; i++) {
                var step = data.routes[k].legs[j].steps[i];

                L.Polyline.fromEncoded(step.polyline, {
                    color: routeColor,
                    weight: 10
                }).addTo(map);

                // افزودن نقطه در ابتدای هر گام
                L.circleMarker([step.start_location[1], step.start_location[0]], {
                    weight: 1,
                    color: "#FFFFFF",
                    radius: 5,
                    fill: true,
                    fillColor: "#9fbef9",
                    fillOpacity: 1.0
                }).addTo(map);
            }
        }
    }
}

function removePolylinesAndCircleMarkers(map) {
    // بررسی تمامی لایه‌های نقشه
    map.eachLayer(function (layer) {
        // اگر لایه از نوع Polyline یا circleMarker باشد، آن را حذف کن
        if (layer instanceof L.Polyline || layer instanceof L.CircleMarker) {
            map.removeLayer(layer);
        }
    });
}
function startTimer(element,minutes) {
    let totalSeconds = minutes * 60;

    function formatTime(seconds) {
        const hrs = Math.floor(seconds / 3600);
        const mins = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;

        if (hrs > 0) {
            return `${hrs}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        } else {
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        }
    }

    const timerInterval = setInterval(function() {
        if (totalSeconds <= 0) {
            clearInterval(timerInterval);
            element.text("زمان به پایان رسید!");
            return;
        }

        element.text(formatTime(totalSeconds));
        totalSeconds--;
    }, 1000);
}

function separate(Number)
{
    Number+= '';
    Number= Number.replace(',', '');
    x = Number.split('.');
    y = x[0];
    z= x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(y))
        y= y.replace(rgx, '$1' + ',' + '$2');
    return y+ z;
}

function replace_just_number(d){
    d.val(d.val().replace(/[^0-9]/g, ''));
}

function recaptcha_init(element) {
    grecaptcha.ready(function() {
        grecaptcha.execute('6LccYTgqAAAAAKjTliGCE0yJu_ZoQDjyXtxoqOsW', {action: 'login'}).then(function(token) {
            element.val(token);
        });
    });
}

