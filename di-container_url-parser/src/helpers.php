<?php

declare(strict_types=1);

function processQueryParams(string $query): array
{
    if (str_starts_with($query, '?')) {
        $query = substr($query, 1, -1);
    }
    return array_map(function (string $item) {
        $kvPair = explode('=', $item);
        $el[$kvPair[0]] = $kvPair[1];
        return $el;
    }, explode("&", $query));
}

function custom_parse_url(string $url): array|int
{
    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        return -1;
    }
    $match = [];
    $result = [];

    if (preg_match('^https*^', $url, $match)) {
        $result['scheme'] = $match[0];
    }
    if (preg_match('#//([^/]+)#', $url, $match)) {
        $result['host'] = $match[1];
    }
    if (preg_match('~//[a-z0-9.]+([^?#]*)~', $url, $match)) {
        $result['path'] = $match[1];
    }
    if (preg_match('~\?[\d\D]+~', $url, $match)) {
        $result['query'] = processQueryParams($match[0]);
    }
    // TODO: Добавить обработку массивов
    return $result;
}