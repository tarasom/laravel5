<?php

namespace App\Services;

use Cache;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class Middleware
{

    /**
     * Logging information about request to remote server and response from him to file
     * @param string $service
     * @return \Closure
     */
    public static function logToFile(string $service): \Closure
    {
        return function (callable $handler) use($service) {
            return function (Request $request, array $options) use ($handler, $service) {
                return $handler($request, $options)->then(
                    function (Response $response) use ($request, $service) {
                        $messageFormat = '{method} {uri} {req_body} RESPONSE: {code} - {res_body}';
                        $formatter     = new MessageFormatter($messageFormat);
                        $message       = "[$service] " . $formatter->format($request, $response);

                        logger()->debug($message);

                        $response->getBody()->rewind();

                        return $response;
                    }
                );
            };
        };
    }


}
