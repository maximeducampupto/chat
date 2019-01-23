<?php

require 'includes/bootstrap.php';
require 'includes/helpers.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && !isset($_SESSION['username']))
{
    $_SESSION['username'] = filterString($_POST['username']);
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <title>Chat</title>
</head>
<body>

<?php if (!isset($_SESSION['username'])) { ?>
    <div id="usernameFormContainer">
        <h1>Choose a username to start chatting!</h1>
        <form action="" method="post" id="usernameForm">
            <input type="text" name="username" id="username" required>
            <input type="submit" value="Go!">
        </form>
    </div>
<?php } ?>


<div class="chatContainer">
    <div id="chatMessages"></div>
    <div id="send">
        <textarea
                name="userMessage"
                id="userMessage"
                cols="30" rows="10"
                placeholder=""></textarea>
        <button type="button" id="sendMessage">Envoyer</button>
    </div>
</div>


<script>
let chatMessages = document.getElementById('chatMessages'),
    timeout,
    button = document.getElementById('sendMessage');


/* Initial request to populate the chatMessages div */
var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {

        refreshMarkup(JSON.parse(this.responseText));
        /* Initiates the refresh timer */
        refreshChat();

        console.log((JSON.parse(this.responseText)));

    }
};
xhttp.open("GET", "api/show.php", true);
xhttp.send();

/* Refresh timer */
function refreshChat() {
    timeout = setTimeout(function() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {

                refreshMarkup(JSON.parse(this.responseText));

                refreshChat();
            }
        };
        xhttp.open("GET", "api/show.php", true);
        xhttp.send();

    }, 10000);
}

/* Empties and repopulate the chatMessages div */
function refreshMarkup(messages)
{
    chatMessages.innerHTML = '';

    if (messages)
    {
        messages.forEach(message => {
            let div = document.createElement('div'),
                p = document.createElement('p'),
                small = document.createElement('small'),
                strong = document.createElement('strong'),
                date = new Date(message.created_at);


            strong.innerHTML = message.username;
            small.innerHTML = dateWithZeros(date);
            p.innerHTML = message.content;

            div.appendChild(strong);
            div.append(small);
            div.appendChild(p);

            div.classList.add('message');

            chatMessages.appendChild(div);
        })
    }
}

/*
Posts request with textarea content
Temporarily updates the client while waiting the next timer refresh
 */
button.addEventListener('click', function() {
    let content = document.getElementById('userMessage').value,
        username = "<?= isset($_SESSION['username']) ? $_SESSION['username'] : 'Anonyme'; ?>";

    if (content.length > 0)
    {
        var contentWithBreaks = content.replace(/\r?\n/g, '<br />');
        var xhttp = new XMLHttpRequest();
        console.log(contentWithBreaks);
        var params = '?content=' + contentWithBreaks + '&username=' + username;

        xhttp.onreadystatechange = function() {

            if(xhttp.readyState == 4 && xhttp.status == 200) {
                if (this.responseText === 'success') {

                    let div = document.createElement('div'),
                        p = document.createElement('p'),
                        small = document.createElement('small'),
                        strong = document.createElement('strong');

                    strong.innerHTML = username;
                    small.innerHTML = "A l'instant";
                    p.innerHTML = content;

                    div.appendChild(strong);
                    div.append(small);
                    div.appendChild(p);

                    div.classList.add('message');

                    chatMessages.appendChild(div);
                }
            }
        };

        xhttp.open("POST", "api/create.php" + params, true);
        xhttp.send();
    }
});


function dateWithZeros(created_at)
{
    let resultHours = (created_at.getHours() < 10 ? '0' : '') + created_at.getHours(),
        resultMinutes = (created_at.getMinutes() < 10 ? '0' : '') + created_at.getMinutes();
    return resultHours + ':' + resultMinutes;
}
</script>
</body>
</html>