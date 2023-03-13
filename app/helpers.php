<?php

use Carbon\Carbon;

if(!function_exists('formatDate')) 
{
    function formatDate($date, $format = 'Y-m-d') {
        return Carbon::createFromDate($date)->format($format);
    }
}

if(!function_exists('diffForHumans')) 
{
    function diffForHumans($date) {
        $date = Carbon::createFromDate($date);
        $publicationDate = now()->subDay()->lt($date) ? $date->diffForHumans() : $date->isoFormat('MMMM Do, Y');

        return $publicationDate;
    }
}