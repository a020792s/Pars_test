<?php

namespace CARiD\Parser;

use DateTime;
use Symfony\Component\DomCrawler\Crawler;

class product
{
    private $idientifier;
    private $name;
    private $price;
    private $images;
    private $video;
    private $pdf;
    private $features;
    private $reviewsQTY;
    private $reviews;
    private $host;

    public function __construct($result, $host)
    {
        $crawler = new Crawler($result);
        $this->idientifier = str_replace("'", "\'", $crawler->filterXPath('//span[@itemprop=\'sku\']')->text());
        $this->name = str_replace("'", "\'", $crawler->filterXPath('//h1[@class=\'name\']')->text());
        $this->price = str_replace("'", "\'", $crawler->filterXPath('//span[@class=\'js-product-price-hide prod-price\']')->text());
        $this->images = str_replace("'", "\'", substr($this->getStringByType($result, '.jpg'), 0, -1));
        $this->video = str_replace("'", "\'", substr($this->getStringByType($result, '.mp4'), 0, -1));
        $this->pdf = str_replace("'", "\'", substr($this->getStringByType($result, '.pdf'), 0, -1));
        $this->features = str_replace("'", "\'", substr($this->getFeaturesFromPage($result), 0, -6));
        $this->reviewsQTY = $this->getRewiewsQTY($result);
        $this->reviews = str_replace("'", "\'", substr($this->getALLRewiews($result), 0, -1));
        $this->host = $host;
    }

    private function getStringByType($result, $type)
    {
        $temp = '';
        $crawler = new Crawler($result);
        foreach ($crawler->filterXPath('//a[contains(@href, \'' . $type . '\') and @class=\'js-product-gallery\']') as $i => $node) {
            $url = $node->getAttribute('href');
            $temp = $temp . $url . ',';
        }
        return $temp;
    }

    private function getFeaturesFromPage($result)
    {
        $temp = '';
        $crawler = new Crawler($result);
        if ($crawler->filterXPath('//p[@class=\'ov_hidden\']')->count() > 0) {
            $html = $crawler->filterXPath('//p[@class=\'ov_hidden\']');
            foreach ($html as $i => $node) {
                $temp = $temp . $node->textContent . '[:os:]';
            }
        } else {
            $html = $crawler->filterXPath('//h3[@id=\'features\']')->nextAll()->filter('ul')->first();
            foreach ($html->filter('li') as $i => $node) {
                $temp = $temp . $node->textContent . '[:os:]';
            }
        }


        return $temp;
    }

    private function getRewiewsQTY($result)
    {
        $crawler = new Crawler($result);
        try {
            return substr(preg_replace('/.*\\(/', '', $crawler->filterXPath('//div[@class=\'prod-review-title app-title app-content-minus app-content-plus\']')->text()), 0, -2);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getALLRewiews($result)
    {
        if ($this->getReviewsQTY() == 0) {
            return "";
        }
        $temp = $this->getRewiewsFromPage($result);;
        if ($this->getReviewsQTY() > 10) {
            curl::getInstance($this->host)->add_headers()->cookie();
            $crawler = new Crawler($result);
            $requestID = $crawler->filterXPath('//span[@class=\'app-btn -icon js-prod-rvw-view-more\']')->attr("data-id");
            for ($i = 10; $i < $this->getReviewsQTY(); $i += 10) {
                $temp = $temp . $this->getRewiewsFromRequest($this->makeRequestForRewiews($requestID, $i));
            }
        }
        return $temp;
    }

    private function makeRequestForRewiews($requestID, $i)
    {
        if (strpos($this->getIdientifier(), 'sp') !== false) {
            $action = "getSuperProductReviews";
        } elseif (strpos($this->getIdientifier(), 'mpn') !== false) {
            $action = "getMpnProductReviews";
        } else {
            $action = "getProductReviews";
        }
        $request = "offset=" . $i . "&type=new&id=" . $requestID . "&action=" . $action;
        try {
            return curl::getInstance($this->host)->post($request)->request("/submit_review.php");
        } catch (\Exception $e) {
            echo "Problems with reviews POST request - " . $e->getCode() . " - " . $e->getMessage();
            return false;
        }
    }

    private function getRewiewsFromPage($result)
    {
        $crawler = new Crawler($result);
        $temp = '';
        $html = $crawler->filterXPath('//span[@itemprop=\'datePublished\']');
        foreach ($html as $i => $node) {
            $temp = $temp . $this->formatDate($node->getAttribute("content")) . ',';
        }
        return $temp;

    }

    private function getRewiewsFromRequest($result)
    {
        $crawler = new Crawler($result);
        $temp = '';
        $html = $crawler->filterXPath('//span[@class=\'posted\']/span[2]');
        foreach ($html as $i => $node) {
            $temp = $temp . $this->formatDate1($node->textContent) . ',';
        }
        return $temp;

    }

    private function formatDate1($string)
    {
        $date = DateTime::createFromFormat('F j, Y', $string);
        return $date->format('m-d-Y');
    }

    private function formatDate($string)
    {
        $date = DateTime::createFromFormat('Y-m-d', $string);
        return $date->format('m-d-Y');
    }

    public function getReviewsQTY()
    {
        return $this->reviewsQTY;
    }

    public function getIdientifier()
    {
        return $this->idientifier;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getImages()
    {
        return $this->images;
    }

    public function getVideo()
    {
        return $this->video;
    }

    public function getPdf()
    {
        return $this->pdf;
    }

    public function getFeatures()
    {
        return $this->features;
    }

    public function getReviews()
    {
        return $this->reviews;
    }

}