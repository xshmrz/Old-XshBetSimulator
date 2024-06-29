<?php

namespace Stevebauman\Location\Drivers;

class IpApiPro extends IpApi
{
    /**
     * {@inheritDoc}
     */
    public function url(string $ip): string
    {
        $key = config('location.ip_api.token');

        return "https://pro.ip-api.com/json/$ip?key=$key&fields=status,message,country,countryCode,region,regionName,city,zip,lat,lon,timezone,currency,isp,org,as,query";
    }
}
