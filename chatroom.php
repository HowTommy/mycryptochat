<?php
    require 'inc/constants.php';
    require 'inc/conf.php';
    require 'inc/init.php';
    require 'inc/functions.php';
    require 'inc/classes.php';
    require 'inc/dbmanager.php';

    $dbManager = new DbManager();

    $chatRoom = $dbManager->GetChatroom($_GET['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Private chat room - MyCryptoChat by HowTommy.net</title>
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <meta name="viewport" content="width=device-width" />
    <link href="styles/myCryptoChat.css" rel="stylesheet" />
    <script src="scripts/modernizr.js"></script>
</head>
<body>
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

            <div id="chatroom"></div>
            <div id="divUsers"><span id="nbUsers">1</span> user(s) online</div>

            <div>
                Name:

    <input type="text" id="userName" /><br />
                <textarea id="textMessage" onkeydown="if (event.keyCode == 13 && !event.shiftKey) { sendMessage(); }"></textarea><br />
                <input type="button" value="Send" id="sendMessage" onclick="sendMessage();" /><br /><br />
				<?php 
					if($chatRoom->isRemovable) {
				?>
					<br /><div id="divButtonRemoveChatroom">
                            <input type="button" value="Remove the chat room" onclick="removeChatroom(<?php if($chatRoom->removePassword != '') { echo 'true'; } else { echo 'false'; } ?>);" />
                        </div>
				<?php
					}
				?>
            </div>
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

    <script type="text/javascript">
        var roomId = '<?php echo htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8'); ?>';
        var dateLastGetMessages = '<?php echo microtime(true) - 24*60*60*365*3; ?>';
    </script>
    <script type="text/javascript" src="scripts/zerobin.js"></script>
    <script type="text/javascript" src="scripts/myCryptoChat.js"></script>
</body>
</html>
