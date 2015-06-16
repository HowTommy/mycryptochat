<?php
require 'inc/constants.php';
require 'inc/functions.php';
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Home - MyCryptoChat by HowTommy.net</title>
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <meta name="viewport" content="width=device-width" />
    <link href="styles/myCryptoChat.css" rel="stylesheet" />
    <script src="scripts/modernizr.js"></script>
</head>
<body>
    <?php
    $showContent = true;
    $configIncluded = false;
    if(is_readable(CONFIG_FILE_NAME)) {
        include CONFIG_FILE_NAME;
        $configIncluded = true;
    } else {
        $showContent = false;
        ?>
        <h2>Error: missing inc/conf.php</h2>
        <p>
            MyCryptoChat can't read the configuration file.<br />
            Copy <strong>inc/conf.template.php</strong> into <strong>inc/conf.php</strong>, and don't forget to <strong>customize it</strong>.
        </p>
    <?php
    }
    if(!is_writable(DB_FILE_NAME)) {
        $showContent = false;
    ?>
    <h2>Error: database access</h2>
    <p>
        MyCryptoChat can't edit the database file.<br />
        Please give all rights to the apache (or current) user on the 'chatrooms.sqlite' file.
    </p>
    <?php
    }
    if (!extension_loaded('PDO')) {
        $showContent = false;
    ?>
    <h2>Error: PDO missing</h2>
    <p>
        The PDO module is missing.<br />
        Please add it and load it to make this website work.
    </p>
    <?php
    }
    if (!extension_loaded('PDO_SQLITE')) {
        $showContent = false;
    ?>
    <h2>Error: PDO SQLite missing</h2>
    <p>
        The PDO SQLite module is missing.<br />
        Please add it and load it to make this website work.
    </p>
    <?php
    }
    if(!is_writable(LOGS_FILE_NAME)) {
        $showContent = false;
    ?>
    <h2>Error: logs file access</h2>
    <p>
        MyCryptoChat can't edit the logs file.<br />
        Please give all rights to the apache (or current) user on the 'logs.txt' file.
    </p>
    <?php
    }
    if (version_compare(phpversion(), '5.4.0', '<')) {
        $showContent = false;
    ?>
    <h2>Error: php version</h2>
    <p>
        The version of php is too low.<br />
        You need at least PHP 5.4 to run this website.
    </p>
    <?php
    }
    if($configIncluded === true && SEED == 'f-rjng24!1r5TRHHgnjrt') {
        $showContent = false;
    ?>
    <h2>Error: the seed was not modified</h2>
    <p>
        The seed that is used to do a better hashing for users is still 'f-rjng24!1r5TRHHgnjrt'<br />
        Please modify its value in 'inc/conf.php'.<br />
        You could may be use '<?php echo randomString(20); ?>', or another.
    </p>
    <?php
    }
    if($showContent) {
    ?>
    <noscript>
        This website needs JavaScript activated to work. 
              <style>
                  div {
                      display: none;
                  }
              </style>
    </noscript>
    <header>
        <div class="content-wrapper">
            <div class="float-left">
                <p class="site-title"><a href="index.php">MyCryptoChat</a></p>
            </div>
            <div class="float-right">
                <section id="login">
                </section>
                <nav>
                    <ul id="menu">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="stats.php">Stats</a></li>
                        <li><a href="about.php">About</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <div id="body">
        <section class="content-wrapper main-content clear-fix">

            <h2>MyCryptoChat</h2>

            <div class="mb20">Chat with friends without anyone spying on what you say!</div>

            <form method="POST" action="newroom.php">
                <label for="nbMinutesToLive">Lifetime of the chat room:</label>
                <select id="nbMinutesToLive" name="nbMinutesToLive">
                    <?php foreach ($allowedTimes as $minutes => $label) { ?>
                        <option value="<?php echo $minutes; ?>"><?php echo $label; ?></option>
                    <?php } ?>
                </select><br />
                <br />
				
				<input type="checkbox" name="isRemovable" id="isRemovable" value="true" onchange="if(this.checked) { $('#divRemovePassword').show(); } else { $('#divRemovePassword').hide(); }" />
                <label for="isRemovable" class="labelIndex">Is removable</label>
				<br />
				<div id="divRemovePassword">
					<br /><label for="removePassword">Password to remove:</label>
					<input type="password" name="removePassword" value="" />
				</div>
				<br />
				
				<input type="checkbox" name="selfDestroys" id="selfDestroys" value="true" />
                <label for="selfDestroys" class="labelIndex">Self-destroys if more than one visitor</label>
				<br />
				
				<br />
                <input type="submit" value="Create a new chat room" />
            </form>
        </section>
    </div>
    <footer>
        <div class="content-wrapper">
            <div class="float-left">
                <p>&copy; 2013 - MyCryptoChat <?php echo MYCRYPTOCHAT_VERSION; ?> by HowTommy.net</p>
            </div>
        </div>
    </footer>
    <script src="scripts/jquery.js"></script>
    <?php } ?>
</body>
</html>
