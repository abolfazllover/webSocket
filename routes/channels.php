<?php

use Illuminate\Support\Facades\Broadcast;
$request=request();

Broadcast::channel('test-channel', function () {
    return true;
});


Broadcast::channel('pc-amir', function ($user, $userId) use ($request) {
    return true;
});




