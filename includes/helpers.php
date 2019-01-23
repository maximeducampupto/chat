<?php

function filterString($string)
{
    return filter_var($string, FILTER_SANITIZE_STRING);
}