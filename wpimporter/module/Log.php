<?php


function f($object, $class = '')
{
    $log = Log::getInstance();
    $log->setActivated(true);
    $log->setFlushActivated(true);
    $log->setPrintActivated(true);
    $log->f($object, $class);
}

function fx($object, $class = '')
{
    f($object, $class);
    exit();
}

function fr($object, $class = '')
{
    $log = Log::getInstance();
    $log->setActivated(true);
    $log->setFlushActivated(true);
    $log->setPrintActivated(true);
    $log->fr($object, $class);
}

function frx($object, $class = '')
{
    fr($object, $class);
    exit();
}

class Log
{

    private static $_instance;
    private $activated;
    private $flushActivated;
    private $printActivated;
    private $selectActivated;
    private $time;

    /**
     * Récupère l'instance de la classe
     * @return SingletonClass
     */
    public static function getInstance()
    {
        if (true === is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    private function __construct()
    {
        ob_start();
        $this->time = microtime(true);
    }

    private function t()
    {
        $time = microtime(true) - $this->time;

        return '<span style="display:inline-block;width:100px">' . round($time, 1) . " s " . chr(9) . " -></span>";
    }

    // fonction <pre>print_r(...)</pre>
    public function pr($obj, $class = '')
    {
        if ($this->activated && $this->printActivated) {
            echo "<pre class='" . $class . "'>";
            echo $this->t();
            print_r($obj);
            echo "<br></pre>";
        }
    }

    // fonction <pre>print_r(...)</pre>
    public function p($obj, $class = '')
    {
        if ($this->activated && $this->printActivated) {
            echo "<div class='" . $class . "'>";
            echo $this->t();
            echo $obj . "</div>";
        }
    }

    // fonction flush
    public function f($chaine, $class = '')
    {

        if ($this->activated && $this->flushActivated) {
            $this->p($chaine, $class);
            ob_flush();
            flush();
        }
    }

    // fonction flush
    public function fr($chaine, $class = '')
    {
        if ($this->activated && $this->flushActivated) {
            $this->pr($chaine, $class);
            ob_flush();
            flush();
        }
    }

    // fonction Select
    public function s($chaine, $class = '')
    {
        if ($this->activated && $this->selectActivated) {
            $this->p($chaine, $class);
            ob_flush();
            flush();
        }
    }

    public function setActivated($bool = true)
    {
        if (is_bool($bool)) {
            $this->activated = $bool;
        }
    }

    public function setFlushActivated($bool)
    {
        if (is_bool($bool)) {
            $this->flushActivated = $bool;
        }
    }

    public function setPrintActivated($bool)
    {
        if (is_bool($bool)) {
            $this->printActivated = $bool;
        }
    }

    public function setSelectActivated($bool)
    {
        if (is_bool($bool)) {
            $this->selectActivated = $bool;
        }
    }

    public static function logError($message, $line, $file) {
        error_log($message . chr(10) . $line . ", " . $file . chr(10) . chr(10), 3, $_SERVER['DOCUMENT_ROOT'] . '/../data/logs/error_log.txt');
    }

    public static function logQuery($query, $params) {
        $message = $query.chr(10).Tools::arrayToString($params);
        error_log(chr(10).'* '.$message, 3, $_SERVER['DOCUMENT_ROOT'] . '/../data/logs/query_log.txt');
    }

}
