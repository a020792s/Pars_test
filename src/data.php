<?php


class data
{
    private $emailForResult;
    private $mailerUsername;
    private $mailerPassword;
    private $mailerSMTP;
    private $mailerPort;
    private $dbServer;
    private $dbUsername;
    private $dbPassword;
    private $dbName;

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
        $ar = $this->getDatafromFile();
        $this->emailForResult = $ar["emailForResult"];
        $this->mailerUsername = $ar["mailerUsername"];
        $this->mailerPassword = $ar["mailerPassword"];
        $this->mailerSMTP = $ar["mailerSMTP"];
        $this->mailerPort = $ar["mailerPort"];
        $this->dbServer = $ar["dbServer"];
        $this->dbUsername = $ar["dbUsername"];
        $this->dbPassword = $ar["dbPassword"];
        $this->dbName = $ar["dbName"];
    }

    private function getDatafromFile()
    {
        $ar = array();
        $descriptor = fopen("gitconfig_file.txt", 'r');
        if ($descriptor) {
            while (($string = fgets($descriptor)) !== false) {
                $temp = explode(": ", substr($string, 0, -1));
                $ar[$temp[0]] = $temp[1];
            }
            fclose($descriptor);

        } else {
            echo 'Impossible to open config_file.txt';
        }
        return $ar;
    }

    public function getEmailForResult()
    {
        return $this->emailForResult;
    }

    public function getMailerUsername()
    {
        return $this->mailerUsername;
    }

    public function getMailerPassword()
    {
        return $this->mailerPassword;
    }

    public function getMailerSMTP()
    {
        return $this->mailerSMTP;
    }

    public function getMailerPort()
    {
        return $this->mailerPort;
    }

    public function getDbServer()
    {
        return $this->dbServer;
    }

    public function getDbUsername()
    {
        return $this->dbUsername;
    }

    public function getDbPassword()
    {
        return $this->dbPassword;
    }

    public function getDbName()
    {
        return $this->dbName;
    }

}