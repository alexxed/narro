<?php

/**
 * Writes log messages to the Firebug Console via QFirebug.
 */
class Zend_Log_Writer_QFirebug extends Zend_Log_Writer_Firebug {

    /**
     * Log a message to the Firebug Console.
     *
     * @param array $event The event data
     * @return void
     */
    protected function _write($event) {
        if (!QFirebug::getEnabled()) {
            return;
        }

        switch($event['priority']) {
            case Zend_Log::WARN:
                return QFirebug::warn($event['message']);
            case Zend_Log::ERR:
            case Zend_Log::ALERT:
            case Zend_Log::CRIT:
            case Zend_Log::EMERG:
                return QFirebug::error($event['message']);
            case Zend_Log::INFO:
            case Zend_Log::NOTICE:
                return QFirebug::info($event['message']);
            default:
                return QFirebug::log($event['message']);
        }
    }
}
