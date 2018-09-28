<?php

namespace CARiD\Parser;

class reportMaker
{
    private $newProductsQuery = "Select NOW.IDIENTIFIER FROM NOW left join WAS using(IDIENTIFIER) where WAS.IDIENTIFIER is NULL";
    private $disappearedProductsQuery = "Select WAS.IDIENTIFIER FROM WAS left join NOW using(IDIENTIFIER) where NOW.IDIENTIFIER is NULL";
    private $reviewedProductsQuery = "Select NOW.IDIENTIFIER FROM NOW INNER JOIN WAS using(IDIENTIFIER) where NOT NOW.REWIEVSQTY=WAS.REWIEVSQTY";
    private $productsQuery = "Select IDIENTIFIER FROM NOW";

    public function createAndSendReports($start, $scriptStart)
    {
        $products = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . "products.csv";
        $new = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . "new_products.csv";
        $disappeared = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . "disappeared_products.csv";
        $reviewed = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . "recently_reviewed_products.csv";

        if (db::getInstance()->isWasTableExist()) {
            $this->createCSV($this->newProductsQuery, $new);
            $this->createCSV($this->disappearedProductsQuery, $disappeared);
            $this->createCSV($this->reviewedProductsQuery, $reviewed);
            $ar = array($new, $disappeared, $reviewed);
            $m = new mailer();
            $m->sendLetter($ar, $start, $scriptStart);
        } else {
            $this->createCSV($this->productsQuery, $products);
            $ar = array($products);
            $m = new mailer();
            $m->sendLetter($ar, $start, $scriptStart);
        }
    }

    private function createCSV($query, $name)
    {
        $result = mysqli_query(db::getInstance()->getConnection(), $query);
        $this->writeFile($name, $result);
    }

    private function writeFile($name, $result)
    {
        unlink($name);
        $fp = fopen($name, 'a');
        fwrite($fp, "IDIENTIFIER" . "\n");
        while ($row = mysqli_fetch_assoc($result)) {
            fwrite($fp, $row["IDIENTIFIER"] . "\n");
        }
        fclose($fp);
    }

}