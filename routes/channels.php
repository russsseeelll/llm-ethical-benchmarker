<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('test-run.{id}', function ($user, $id) {
    return true;   
});
