<?php

// logger.php
function writeToLog($message) {
    $message = validateLogInput($message);
    $logFile = '/var/log/websitelogs/websitelog.log'; // Website log file
    $errorLogFile = '/var/log/websitelogs/errorlog.log'; // Error log file
    $maxFileSize = 5 * 1024 * 1024;

    if (file_exists($logFile) && filesize($logFile) > $maxFileSize) {
        // Rotate log file
        rename($logFile, $logFile . '.' . time());
    }

    $currentTimestamp = date('Y-m-d H:i:s');
    $logMessage = $currentTimestamp . ": " . $message . "\n";

    file_put_contents($logFile, $logMessage, FILE_APPEND);

    // Error handling. Writing to an alternative log file
    if (file_put_contents($logFile, $logMessage, FILE_APPEND) === false) {
        $errorLogMessage = $currentTimestamp . ": Failed to write to log: " . $message . "\n";
        file_put_contents($errorLogFile, $errorLogMessage, FILE_APPEND);
    }
}

function validateLogInput($message) {
    // Remove any newline characters to prevent log injection
    return str_replace(array("\n", "\r"), '', $message);
}