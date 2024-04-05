<?php

namespace PicPerf\StatamicPicPerf\Service;

use PicPerf\StatamicPicPerf\Trait\Configurable;

class RegisterSitemapService
{
    use Configurable;

    public function handle()
    {
        $host = rtrim($this->getConfig('host'), '/');
        $sitemapLine = "\nSitemap: {$host}/picperf/sitemap.xml";
        $robots = $this->getRobotsTxt();

        if (strpos($robots, $sitemapLine) !== false) {
            $this->log("Sitemap already exists in robots.txt file.");
            return;
        }

        $robots .= $sitemapLine;

        $this->putRobotsTxt($robots);

        $this->log("Sitemap added to robots.txt file.");
    }

    public function getRobotsTxt()
    {
        return file_get_contents(public_path('robots.txt'));
    }

    public function putRobotsTxt($contents)
    {
        file_put_contents(public_path('robots.txt'), $contents);
    }

    private function log($message)
    {
        echo "PicPerf: {$message}\n";
    }
}
