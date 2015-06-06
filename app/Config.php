<?php

/**
 * Gets global settings for the app from ini-file.
 */
class Config {

    /*
     * Global settings for the app.
     */
    public $settings;
    public $players;

    function __construct() {
        $this->settings = array();

        // get host id = [machinename]
        $host = php_uname('n');

        // get settings
        $ini = parse_ini_file('app.ini', true);

        // build config
        if (isset($ini[$host])) {
            // host specific values
            $this->settings = $ini[$host];
        }
        if (isset($ini['common'])) {
            // common values (duplicate keys are not overwritten)
            $this->settings += $ini['common'];
        }
        $this->setLocal();

        ini_set('display_errors', $this->settings['display_errors']);
        ini_set('error_reporting', $this->settings['error_reporting']);

        if (isset($this->settings['max_execution_time']) && !$this->settings['safe_mode']) {
            set_time_limit($this->settings['max_execution_time']);
        }
    }

    function setLocal() {
        $host = $_SERVER['HTTP_HOST'];
        $this->settings['local'] =
            (strpos($host, 'dev.movies13') !== false ||
                strpos($host, 'localhost') === 0 ||
                strpos($host, '192.168.0.187') === 0);
    }

}
