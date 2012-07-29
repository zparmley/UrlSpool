#!/bin/bash
php UrlSpool.php 'http://example.com/$0<$0!=flush~/$1<$0=set~/$2>>' '[["get", "set", "flush"], ["mek", "wek", "bek"], [1, 2, 3]]' 10