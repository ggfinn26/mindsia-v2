<?php

use Illuminate\Support\Facades\Request;

if (! function_exists('apex_url')) {
    /**
     * Build an absolute URL on the apex domain (config('app.domain')), keeping the
     * current request's scheme and port. Needed because route('home') has no domain
     * constraint and otherwise inherits the current subdomain host.
     */
    function apex_url(string $suffix = ''): string
    {
        $port = Request::getPort();
        $portPart = in_array($port, [80, 443, null], true) ? '' : ':'.$port;

        return Request::getScheme().'://'.config('app.domain').$portPart.$suffix;
    }
}
