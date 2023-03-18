<?php
$host = 'localhost';
$port = '8080';
$null = NULL;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($socket, 0, $port);
socket_listen($socket);

$clients = array($socket);
$users = array();

while (true) {
    $changed = $clients;
    socket_select($changed, $null, $null, 0, 10);

    if (in_array($socket, $changed)) {
        $new_socket = socket_accept($socket);
        $clients[] = $new_socket;

        $header = socket_read($new_socket, 1024);
        perform_handshaking($header, $new_socket, $host, $port);

        socket_getpeername($new_socket, $ip);
        $response = mask(json_encode(array('type' => 'system', 'message' => $ip . ' connected')));
        send_message($response);
    }

    foreach ($changed as $changed_socket) {
        while (socket_recv($changed_socket, $buf, 1024, 0) >= 1) {
            $received_text = unmask($buf);
            $tst_msg = json_decode($received_text);
            $user_name = $tst_msg->username;
            $user_message = $tst_msg->message;
    
            $response_text = mask(json_encode(array(
                'type' => 'usermsg',
                'username' => $user_name,
                'message' => $user_message
            )));
    
            send_message($response_text);
        }
    
        $buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
        if ($buf === false) {
            $found_socket = array_search($changed_socket, $clients);
            $user_name = $users[$changed_socket];
            unset($clients[$found_socket]);
            unset($users[$changed_socket]);
    
            $response = mask(json_encode(array('type' => 'system', 'message' => $user_name . ' left the chat')));
            send_message($response);
        }
    }
    

        $buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
        if ($buf === false) {
            $found_socket = array_search($changed_socket, $clients);
            $user_name = $users[$changed_socket];
            unset($clients[$found_socket]);
            unset($users[$changed_socket]);

            $response = mask(json_encode(array('type' => 'system', 'message' => $user_name . ' left the chat')));
            send_message($response);
        }
    }
}

socket_close($socket);

function send_message($msg) {
    global $clients;
    foreach($clients as $client) {
        @socket_write($client, $msg, strlen($msg));
    }
    return true;
}

function mask($text) {
    $b1 = 0x80 | (0x1 & 0x0f);
    $length = strlen($text);

    if ($length <= 125) {
        return pack('CC', $b1, $length) . $text;
    } else if ($length > 125 && $length < 65536) {
        return pack('CCn', $b1, 126, $length) . $text;
    } else if ($length >= 65536) {
        return pack('CCNN', $b1, 127, $length) . $text;
    }
}

function unmask($text) {
    $length = ord($text[1]) & 127;
    if ($length ==

<?php
    $host = 'localhost';
    $port = '8080';
    $null = NULL;

    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
    socket_bind($socket, 0, $port);
    socket_listen($socket);

    $clients = array($socket);

    while (true) {
        $changed = $clients;
        socket_select($changed, $null, $null, 0, 10);

        if (in_array($socket, $changed)) {
            $new_socket = socket_accept($socket);
            $clients[] = $new_socket;

            $header = socket_read($new_socket, 1024);
            perform_handshaking($header, $new_socket, $host, $port);

            socket_getpeername($new_socket, $ip);
            $response = mask(json_encode(array('type' => 'system', 'message' => $ip . ' connected')));
            send_message($response);
        }

        foreach ($changed as $changed_socket) {
            while (socket_recv($changed_socket, $buf, 1024, 0) >= 1) {
                $received_text = unmask($buf);
                $tst_msg = json_decode($received_text);
                $user_name = $tst_msg->username;
                $user_message = $tst_msg->message;

                $response_text = mask(json_encode(array('type' => 'usermsg', 'username' => $user_name, 'message' => $user_message)));
                send_message($response_text);
                break 2;
            }

            $buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
            if ($buf === false) {
                $found_socket = array_search($changed_socket, $clients);
                socket_getpeername($changed_socket, $ip);
                unset($clients[$found_socket]);

                $response = mask(json_encode(array('type' => 'system', 'message' => $ip . ' disconnected')));
                send_message($response);
            }
        }
    }

    socket_close($socket);

    function send_message($msg) {
        global $clients;
        foreach($clients as $changed_socket) {
            @socket_write($changed_socket, $msg, strlen($msg));
        }
        return true;
    }

    function mask($text) {
        $b1 = 0x80 | (0x1 & 0x0f);
        $length = strlen($text);

        if ($length <= 125) {
            return pack('CC', $b1, $length) . $text;
        } else if ($length > 125 && $length < 65536) {
            return pack('CCn', $b1, 126, $length) . $text;
        } else if ($length >= 65536) {
            return pack('CCNN', $b1, 127, $length) . $text;
        }
    }

    function unmask($payload) {
        $length = ord($payload[1]) & 127;
        if ($length == 126) {
            $masks = substr($payload, 4, 4);
            $data = substr($payload, 8);
        }
        elseif ($length == 127) {
            $masks = substr($payload, 10, 4);
            $data = substr($payload, 14);
        }
        else {
            $masks = substr($payload, 2, 4);
            $data = substr($payload, 6);
        }
    
        $text = '';
        for ($i = 0; $i < strlen($data); ++$i) {
            $text .= $data[$i] ^ $masks[$i % 4];
        }
    
        return $text;
    }
    
    while (true) {
        $changed = $clients;
        socket_select($changed, $null, $null, 0, 10);
    
        if (in_array($socket, $changed)) {
            $new_socket = socket_accept($socket);
            $clients[] = $new_socket;
    
            $header = socket_read($new_socket, 1024);
            perform_handshaking($header, $new_socket, $host, $port);
    
            socket_getpeername($new_socket, $ip);
            $response = mask(json_encode(array('type' => 'system', 'message' => $ip . ' connected')));
            send_message($response);
        }
    
        foreach ($changed as $changed_socket) {
            while (socket_recv($changed_socket, $buf, 1024, 0) >= 1) {
                $received_text = unmask($buf);
                $tst_msg = json_decode($received_text);
                $user_name = $tst_msg->username;
                $user_message = $tst_msg->message;
    
                $response_text = mask(json_encode(array('type' => 'usermsg', 'username' => $user_name, 'message' => $user_message)));
                send_message($response_text);
                break 2;
            }
    
            $buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
            if ($buf === false) {
                $found_socket = array_search($changed_socket, $clients);
                socket_getpeername($changed_socket, $ip);
                unset($clients[$found_socket]);
    
                $response = mask(json_encode(array('type' => 'system', 'message' => $ip . ' disconnected')));
                function unmask($payload) {
                    $length = ord($payload[1]) & 127;
                    if ($length == 126) {
                        $masks = substr($payload, 4, 4);
                        $data = substr($payload, 8);
                    } elseif ($length == 127) {
                        $masks = substr($payload, 10, 4);
                        $data = substr($payload, 14);
                    } else {
                        $masks = substr($payload, 2, 4);
                        $data = substr($payload, 6);
                    }
                    
                    $text = '';
                    for ($i = 0; $i < strlen($data); ++$i) {
                        $text .= $data[$i] ^ $masks[$i % 4];
                    }
                    return $text;
                }
                