<?php

namespace CARiD\Parser;

use mysqli;

class db
{
    private $connection;
    private static
        $instance = null;


    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->connection = new mysqli(data::getInstance()->getDbServer(), data::getInstance()->getDbUsername(), data::getInstance()->getDbPassword(), data::getInstance()->getDbName());
        if($this->connection->connect_error){
            die('MySQL Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
        }
        else
        {
            $this->connection->set_charset('utf8');
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function insertProduct($product)
    {
        $query = "INSERT INTO NOW (IDIENTIFIER, NAME, PRICE, IMAGES,VIDEO,PDF,FEATURES,REWIEVSQTY,REWIEVS) VALUES ('" . $product->getIdientifier() . "','" . $product->getName() . "','" . $product->getPrice() . "','" . $product->getImages() . "','" . $product->getVideo() . "','" . $product->getPdf() . "','" . $product->getFeatures() . "','" . $product->getReviewsQTY() . "','" . $product->getReviews() . "')";
        mysqli_query($this->getConnection(), $query);
    }

    public function isWasTableExist()
    {
        $query = "SELECT * FROM WAS limit 1";
        $result = mysqli_query($this->getConnection(), $query);
        return $result != false;
    }

    public function renameOldAndCreateNewTable()
    {
        $query = "Drop TABLE WAS";
        mysqli_query($this->getConnection(), $query);
        $query = "RENAME TABLE `NOW` TO `WAS`";
        mysqli_query($this->getConnection(), $query);
        $query = "CREATE TABLE NOW (
                          IDIENTIFIER TEXT,
                          NAME TEXT,
                          PRICE TEXT,
                          IMAGES LONGTEXT,
                          VIDEO LONGTEXT,
                          PDF LONGTEXT,
                          FEATURES LONGTEXT,
                          REWIEVSQTY TEXT,
                          REWIEVS LONGTEXT
                          )";
        mysqli_query($this->getConnection(), $query);
    }
}