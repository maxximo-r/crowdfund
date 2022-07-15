<?php

/**
 *
 *
 * @author maxximo
 */
class CrwdfndMessages {

    private $messages;
    private $session_key;

    public function __construct() {
        $this->messages = get_option('crwdfnd-messages');
        $this->sesion_key = $_COOKIE['crwdfnd_session'];
    }

    public function get($key) {
        $combined_key = $this->session_key . '_' . $key;
        if (isset($this->messages[$combined_key])) {
            $m = $this->messages[$combined_key];
            unset($this->messages[$combined_key]);
            update_option('crwdfnd-messages', $this->messages);
            return $m;
        }
        return '';
    }

    public function set($key, $value) {
        $combined_key = $this->session_key . '_' . $key;
        $this->messages[$combined_key] = $value;
        update_option('crwdfnd-messages', $this->messages);
    }

}
