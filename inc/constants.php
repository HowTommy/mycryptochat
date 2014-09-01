<?php
// modify it: replace 'f-rjng24!1r5TRHHgnjrt' by some random characters
define('SEED', 'f-rjng24!1r5TRHHgnjrt');

define('DEFAULT_NB_MINUTES_TO_LIVE', 30);

// maximum lifetime for a chat room (0 = unlimited)
define('NB_MINUTES_TO_LIVE_MAX', 0);

// number of messages to keep
define('NB_MESSAGES_TO_KEEP', 100);

// number of days to delete an idle chat room
define('DAYS_TO_DELETE_IDLE_CHATROOM', 60);

define('NB_SECONDS_USER_TO_BE_DISCONNECTED', 35);

define('DB_FILE_NAME', 'db/chatrooms.sqlite');
define('LOGS_FILE_NAME', 'db/logs.txt');

define('MYCRYPTOCHAT_VERSION', 'v1.0.4');
?>