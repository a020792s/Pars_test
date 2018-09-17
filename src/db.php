<?php

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
        $this->connection->set_charset('utf8');
        if (mysqli_connect_error()) {
            trigger_error("Failed to connect to MySQL: " . mysqli_connect_error(),
                E_USER_ERROR);
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