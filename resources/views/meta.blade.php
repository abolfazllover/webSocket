<link rel="icon" href="{{route('home')}}/img/min-logo.png">

<link rel="stylesheet" href="{{route('home')}}/css/semantic.css">
<link rel="stylesheet" href="{{route('home')}}/css/app.css">
<link rel="stylesheet" href="{{route('home')}}/css/semantic-ui-alerts.css">
{{--<link rel="stylesheet" href="{{route('home')}}/css/swiper.css">--}}
<link rel="stylesheet" href="{{route('home')}}/css/toast-plugin-styles.css">
<meta name="google-site-verification" content="Re4-MxO8cUh2vYjKIQ-67QLrqDlmfmvT1OVFs3mLAbU" />


<script src="{{route('home')}}/js/jq.js"></script>
<script src="{{route('home')}}/js/all.js"></script>
<script src="{{route('home')}}/js/basic.js"></script>
{{--<script src="{{route('home')}}/js/swiper.js"></script>--}}
<script src="{{route('home')}}/js/toast-plugin-min.js"></script>


<script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>
{{--<link--}}
{{--    rel="stylesheet"--}}
{{--    href="{{route('home')}}/slider.css"--}}
{{--/>--}}

{{--<script src="{{route('home')}}/slider.js"></script>--}}

<style>
    @media only screen and (max-width: 900px) {
        h1,h2,h3,h4,.header{
            font-size: 1rem!important;
        }
        .only_desk{
            display: none!important;
        }
    }

    @media only screen and (min-width: 900px) {
        .only_mobile{
            display: none!important;
        }
    }

    @font-face {
        font-family: 'normal';
        src: url('{{route('home')}}/fonts/YekanBakh-Medium.ttf') format('truetype');
    }
    @font-face {
        font-family: 'bold';
        src: url('{{route('home')}}/fonts/YekanBakh-Heavy.ttf') format('truetype');
    }

    .ui.segment{
        border-radius: 15px;
    }

    .relative{
        position: relative;
    }

    .black{
        color: #232323!important;
    }
    .gray{
        color: #626262 !important;
    }
    .light{
        color: #FFFFFF!important;
    }
    .red{
        color: #FF282A;
    }

    body,a,div,span,p,input,textarea,h5,h6,button,.label,label{
        font-weight: normal!important;
        color: inherit;
        font-family: 'normal'!important;
    }
    .ui.table thead th {
        font-family: 'bold'!important;
    }
    h1,h2,h3,h4,.header{
        color: inherit;
        font-family: 'bold'!important;
        font-weight: normal;
    }

    .only_desk{
        display: block;
    }


</style>

@if(!request()->routeIs('panel') && !request()->routeIs('login') && !request()->routeIs('panel_method'))
<script type="text/javascript">window.$crisp=[];window.CRISP_WEBSITE_ID="76f69413-f6aa-47ff-829a-d9e602b9b965";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>
@endif

