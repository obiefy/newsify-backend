<?php

use Carbon\Carbon;

if(!function_exists('formatDate')) 
{
    function formatDate($date) {
        $date = Carbon::createFromDate($date);
        $publicationDate = now()->subDay()->lt($date) ? $date->diffForHumans() : $date->isoFormat('MMMM Do, Y');

        return $publicationDate;
    }
}