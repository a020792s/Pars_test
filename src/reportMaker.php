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
        if (db::getInstance()->isWasTableExist()) {
            $this->createCSV($this->newProductsQuery,"files/new_products.csv");
            $this->createCSV($this->disappearedProductsQuery,"files/disappeared_products.csv");
            $this->createCSV($this->reviewedProductsQuery,"files/recently_reviewed_products.csv");
            $ar = array("files/new_products.csv", "files/disappeared_products.csv", "files/recently_reviewed_products.csv");
            $m = new mailer();
            $m->sendLetter($ar, $start, $scriptStart);
        } else {
            $this->createCSV($this->productsQuery,"files/products.csv");
            $ar = array("/home/user/server/pars_test.cc/files/products.csv");
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