<?php
namespace Explorer\REST;

use Explorer\Explorer;

class ExplorerService {
    public function addUrl($urlArray) {
        if (count($urlArray) == 0) {
            return 0;
        }

        Explorer::Instance()->addUrlList($urlArray, time());

        return 1;
    }
}