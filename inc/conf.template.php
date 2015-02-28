<?php
// modify it: replace 'f-rjng24!1r5TRHHgnjrt' by some random characters
define('SEED', 'f-rjng24!1r5TRHHgnjrt');

// number of messages to keep
define('NB_MESSAGES_TO_KEEP', 100);

// number of days to delete an idle chat room
define('DAYS_TO_DELETE_IDLE_CHATROOM', 60);

define('NB_SECONDS_USER_TO_BE_DISCONNECTED', 35);

$allowedTimes = array(
    5 => '5 minutes',
    30 => '30 minutes',
    60 => '1 hour',
    240 => '4 hours',
    1440 => '1 day',
    10080 => '7 days',
    40320 => '30 days',
    525960 => '1 year',
    0 => 'Unlimited'
);
