<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('schedule.{id}', function ($user, $id) {
    return true;
});
