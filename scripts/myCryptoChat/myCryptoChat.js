function sendMessage() {
    if ($.trim($("#userName").val()) == "") {
        alert("Please choose a name!");
    } else if (pageKey() == "" || pageKey() == "=") {
        alert("The key is missing (the part of the website url after '#').");
    } else {
        if ($.trim($("#textMessage").val()) != "") {

            var key = pageKey();

            $.post("sendMessage.php", { roomId: roomId, user: zeroCipher(key, $.trim($("#userName").val())), message: zeroCipher(key, $.trim($("#textMessage").val())) }, function (data) {
                if (data != false) {
                    $("#textMessage").val("");
                    $("#textMessage").focus();
                    getMessages(false);
                } else {
                    // error : the message was not recorded
                    alert("An error occured... :(");
                }
            });
        }
    }
}

var isRefreshTitle = false;
var refreshTitleInterval;
var nbIps = 1;

function getMessages(changeTitle) {
    var key = pageKey();
    $.post("getMessages.php", { roomId: roomId, dateLastGetMessages: dateLastGetMessages, nbIps: nbIps }, function (data) {
        if (data == "noRoom") {
            // closed conversation
            $("#chatroom").html("<i>This conversation is over... You should start a new one to keep talking!</i>");
            stopTimerCheck();
        } else if (data == "destroyed") {
            // closed conversation
            $("#chatroom").html("<i>This conversation self-destroyed. It was only created for one visitor.</i>");
            stopTimerCheck();
        } else if (data != "noNew") {
            if (data.chatLines == undefined) {
                $("#nbUsers").html(data.nbIps);
            } else if (data.chatLines == "") {
                if (key == "" || key == "=") {
                    document.location.hash = "#" + sjcl.codec.base64.fromBits(sjcl.random.randomWords(8, 0), 0);
                }
                $("#chatroom").html("<i>No messages yet...</i>");
            } else if (key == "" || key == "=") {
                $("#chatroom").html("<i>The key is missing (the part of the website url after '#').</i>");

                stopTimerCheck();
            } else {
                var hasErrors = false;
                var hasElements = false;
                var chatRoom = $("#chatroom");
                chatRoom.html("");

                nbIps = data.nbIps;
                $("#nbUsers").html(data.nbIps);

                for (i = 0; i < data.chatLines.length; i++) {
                    try {
                        var user = zeroDecipher(key, data.chatLines[i].userId);
                        var decryptedMessage = zeroDecipher(key, data.chatLines[i].message);

                        hasElements = true;

                        if (vizhash.supportCanvas()) {
                            var vhash = vizhash.canvasHash(data.chatLines[i].hash, 15, 10);
                            chatRoom.append("<span class='chathour'>(" + getDateFromTimestamp(data.chatLines[i].date) + ")</span> ");
                            chatRoom.append(vhash.canvas);
                            chatRoom.append(" <a onclick='addText(\" @" + htmlEncode(user) + ": \"); return false;' class='userNameLink' href='#'><b>" + htmlEncode(user) + "</b></a> : " + replaceUrlTextWithUrl(htmlEncode(decryptedMessage)).replace(/(?:\r\n|\r|\n)/g, '<br />') + "<br />");
                        } else {
                            chatRoom.append("<i>" + data.chatLines[i].date + "</i> - <a onclick='addText(\" @" + htmlEncode(user) + ": \"); return false;' class='userNameLink' href='#'><b>" + htmlEncode(user) + "</b></a> : ");
                            chatRoom.append(" <b>" + htmlEncode(user) + "</b> : " + replaceUrlTextWithUrl(htmlEncode(decryptedMessage)).replace(/(?:\r\n|\r|\n)/g, '<br />') + "<br />");
                        }
                    } catch (e) {
                        hasErrors = true;
                    }
                }
                if (!hasElements && hasErrors) {
                    // wrong key error
                    chatRoom.html("The key seems to be corrupted. Are you sure that you copied the full URL (with #xxxxxxxxxxxxxxxx-xxxxxxx-xxxxxxxx) ?");

                    stopTimerCheck();
                } else {
                    var objDiv = document.getElementById("chatroom");
                    objDiv.scrollTop = objDiv.scrollHeight;
                    dateLastGetMessages = data.dateLastGetMessages;

                    if (changeTitle && !isRefreshTitle) {
                        refreshTitleInterval = setInterval(
                            function () {
                                if (document.title == "Private chat room - MyCryptoChat by HowTommy.net") {
                                    document.title = "New messages ! - MyCryptoChat by HowTommy.net";
                                } else {
                                    document.title = "Private chat room - MyCryptoChat by HowTommy.net";
                                }
                            }, 3000);
                        isRefreshTitle = true;
                    }
                }
            }
        }
    });
}

function replaceUrlTextWithUrl(content) {
    var re = /((http|https|ftp):\/\/[\w?=&.\/-;#@~%+-]+(?![\w\s?&.\/;#~%"=-]*>))/ig;
    content = content.replace(re, '<a href="$1" rel="nofollow">$1</a>');
    re = /((magnet):[\w?=&.\/-;#@~%+-]+)/ig;
    content = content.replace(re, '<a href="$1">$1</a>');
    return content;
}

function stopRefreshTitle() {
    if (isRefreshTitle) {
        clearInterval(refreshTitleInterval);
        document.title = "Private chat room - MyCryptoChat by HowTommy.net";
        isRefreshTitle = false;
    }
}

function htmlEncode(value) {
    return $('<div/>').text(value).html();
}

var checkIntervalTimer;

function stopTimerCheck() {
    clearInterval(checkIntervalTimer);
}

function getDateFromTimestamp(date) {
    var date = new Date(date * 1000);
    var hours = date.getHours();
    var minutes = date.getMinutes();
    hours = hours > 9 ? hours : "0" + hours;
    minutes = minutes > 9 ? minutes : "0" + minutes;

    return hours + ':' + minutes;
}

function removeChatroom(withPassword) {
    if (confirm('Are you sure?')) {
        var removePassword = '';
        if (withPassword) {
            var removePassword = prompt("Please enter the password to remove the chat room", "");
        }
        $.post("removeChatroom.php", { roomId: roomId, removePassword: removePassword }, function (data) {
            if (data == "error") {
                alert('An error occured');
            } else if (data == "wrongPassword") {
                alert('Wrong password');
            } else if (data == "removed") {
                alert('The chat room has been removed.');
                window.location = 'index.php';
            }
        });
    }
}

function addText(text) {
    var editor = $('#textMessage');
    var value = editor.val();
    editor.val("");
    editor.focus();
    editor.val(value + text);
}

$(function () {
    getMessages(false);

    // try to get new messages every 15 seconds
    checkIntervalTimer = setInterval("getMessages(true)", 15000);

    $('body').on('mousemove', stopRefreshTitle);
});