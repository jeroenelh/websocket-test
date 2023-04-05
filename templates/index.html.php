<html>
<head>
    <title>Websocket test</title>
</head>
<body>
    <textarea></textarea>


    <script type="text/javascript">
        let socket = new WebSocket("wss://localhost:8181");
        socket.onopen = function (event) {
            socket.send("WebSocket is really cool");
        };

        socket.onmessage = function (event) {
            console.log(event.data);
        }

        socket.onerror = function (event) {
            console.log("ERROR");
        }

        socket.onclose = function (event) {
            console.log("CLOSE " + event.code);
        }
    </script>
</body>
</html>