#!/usr/bin/env php
<?php

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    require __DIR__ . '/../../../../vendor/autoload.php';
}

use Opf\ScimServerPhp\Firebase\JWT\JWT;

/**
 * A function that prints the usage help message to standard output
 */
function showUsage()
{
    fwrite(
        STDOUT,
        "Usage:
  generate_jwt.php -u=<username> -s=<secret>
  generate_jwt.php --username=<username> --secret=<secret>
  generate_jwt.php (-h | --help)\n"
    );
}

/**
 * Generate a JWT for a given user
 *
 * @param string $username The username of the user we generate a JWT for
 * @param string $secret The JWT secret signing key
 * @return string The JWT of the user
 */
function generateJwt(string $username, string $secret): string
{
    $jwtPayload = array(
        "user" => $username
    );

    return JWT::encode($jwtPayload, $secret, "HS256");
}


// Specify the CLI options, passed to getopt()
$shortOptions = "hu:s:";
$longOptions = ["help", "username:", "secret:"];

// Obtain the CLI args, passed to the script via getopt()
$cliOptions = getopt($shortOptions, $longOptions);

// If there was some issue with the CLI args, we show the help message
if ($cliOptions === false) {
    showUsage();
    exit(1);
}

// We check if a username was provided
if (
    (isset($cliOptions["u"]) || isset($cliOptions["username"]))
    && (isset($cliOptions["s"]) || isset($cliOptions["secret"]))    
) {
    $username = isset($cliOptions["u"]) ? $cliOptions["u"] : $cliOptions["username"];
    $secret = isset($cliOptions["s"]) ? $cliOptions["s"] : $cliOptions["secret"];
} else {
    // If no username or secret was provided, we let the user know
    fwrite(STDERR, "A username and a secret JWT key must be provided\n");
    showUsage();
    exit(1);
}

$jwt = generateJwt($username, $secret);
fwrite(STDOUT, "$jwt\n");
exit(0);
