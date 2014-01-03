<?php

/**
 * This should be run from a webserver rather than CLI. You must edit the first
 * 4 constants of the TempData class to integrate with MySQL.
 */

$td = new TempData();
if (isset($_REQUEST['ajax']))
{
    $td->ajax();
}
else
{
    $td->showPage();
}

class TempData
{
    /*********************** MODIFY THESE TO GET MYSQL WORKING *************************/
    const DB_HOST      = '';
    const DB_USER      = '';
    const DB_PASS      = '';
    const DB_SCHEMA    = '';
    
    private $_db       = null;
    private $_db_table = 'sfo_temps';
    private $_data     = null;
    
    const WBAN         = 23234;
    const STNID        = 'n/a';
    const PRIOR        = 'N';
    const QCNAME       = 'VER2';
    const WHICH        = 'ASCII Download (Hourly Obs.) (10A)';
    const URL          = 'http://cdo.ncdc.noaa.gov/qclcd/QCLCD';
    
    private $_year     = null;
    private $_month    = null;
    private $_day      = null;

    public function __construct()
    {
        if (!isset($_REQUEST['date']) ||
            !ctype_digit(strtotime($_REQUEST['date'])) ||
            strtotime($_REQUEST['date']) > time())
        {
            // If anything isn't right, use yesterday as the gold standard.
            $dateArray = explode(" ", date("Y m d", strtotime("yesterday")));
        }
        else
        {
            // I don't trust your input to contain leading zeros or not, so let's let PHP do the work for us.
            $dateArray = explode(" ", date("Y m d", strtotime($_REQUEST['date'])));
        }

        list($y, $m, $d) = $dateArray;

        $this->_year  = $y;
        $this->_month = $m;
        $this->_day   = $d;

        $this->_setupDb()
            ->_getData();
    }

    private function _setupDb()
    {
        $this->_db = new mysqli(self::DB_HOST, self::DB_USER, self::DB_PASS, self::DB_SCHEMA);

        if ($this->_db->connect_error)
        {
            die('Connect Error (' . $mysqli->connect_errno . ') '
                . $mysqli->connect_error);
        }

        $query = $this->_db->query('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = "'.$this->_db_table.'" AND TABLE_SCHEMA=database()');
        if ($query->num_rows == 0)
        {
            $this->_createTable();
        }

        return $this;
    }

    private function _createTable()
    {
        echo "Missing table (".$this->_db_table."). Creating now.\n";

        $query = "CREATE TABLE ".$this->_db_table." (
            id INT(11) NOT NULL auto_increment,
            datetime BIGINT(20) UNIQUE,
            temp INT(11),
            primary KEY (id))";

        $this->_db->query($query);
    }

    private function _getData()
    {
        // Get data from DB.
        $query = $this->_db->query("SELECT * FROM `".$this->_db_table."` WHERE (`datetime` BETWEEN ".strtotime($this->_month."/".$this->_day."/".$this->_year)." AND ".strtotime($this->_month."/".$this->_day."/".$this->_year." +1 days").")");

        if ($query->num_rows == 0)
        {
            $this->_getDataFromSite();
            return $this;
        }

        $this->_data = $query;

        return $this;
    }

    private function _getDataFromSite()
    {
        $queryArray             = array();
        $queryArray['reqday']   = $this->_day;
        $queryArray['stnid']    = self::STNID;
        $queryArray['prior']    = self::PRIOR;
        $queryArray['qcname']   = self::QCNAME;
        $queryArray['VARVALUE'] = self::WBAN.$this->_year.$this->_month;
        $queryArray['which']    = self::WHICH;

        $data = explode("\n",file_get_contents(self::URL."?".http_build_query($queryArray)));

        foreach ($data as $k => $line)
        {
            if (substr($line, 0, 5) != self::WBAN)
            {
                continue;
            }

            list(, $date, $time, , , , , , , , $temp) = explode(",",$line);

            $datetime = strtotime(substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,4)." ".substr($time,0,2).":".substr($time,2,2).":00");

            $query = $this->_db->query("INSERT INTO `".$this->_db_table."` (`id`,`datetime`,`temp`) VALUES('',".$datetime.", ".$temp.")");

            $this->_data[] = array(
                'datetime' => $datetime,
                'temp'     => $temp
            );

        }


        return $this;
    }

    public function ajax()
    {
        $this->_showData();
    }

    private function _showData()
    {
        $tempArray = array();

        $data = View::factory('./views/data.php');
        if (($this->_data == null ||
            $this->_data == false ||
            count($this->_data) == 0) &&
                (!is_object($this->_data) ||
                !is_array($this->_data)))
        {
            $data->json = json_encode("no data");
            echo $data->render();
            die();
        }

        if (is_object($this->_data))
        {
            while ($arr = $this->_data->fetch_assoc())
            {
                $tempArray[] = $arr['temp'];
            }
        }
        elseif (is_array($this->_data))
        {
            foreach ($this->_data as $arr)
            {
                $tempArray[] = $arr['temp'];
            }
        }

        $min = min($tempArray);
        $max = max($tempArray);
        $avg = round(array_sum($tempArray)/count($tempArray),2);

        //echo $min." ".$max." ".$avg."\n";
        $data->json = json_encode(array(
            'min' => $min,
            'max' => $max,
            'avg' => $avg));
        echo $data->render();
    }

    public function showPage()
    {
        $data = View::factory('./views/page.php');
        $data->date = $this->_month."/".$this->_day."/".$this->_year;
        echo $data->render();
    }
}

class View
{
    private $_data = array();
    private $_file = '';

    private function __construct($file)
    {
        $this->set_filename($file);
    }

    public static function factory($file)
    {
        return new View($file);
    }

    public function set_filename($file)
    {
        $this->_file = $file;
    }

    protected static function capture($file, $data)
    {
        extract($data, EXTR_SKIP);

        ob_start();

        try
        {
            include($file);
        }
        catch (Exception $e)
        {
            ob_end_clean();

            throw $e;
        }

        return ob_get_clean();
    }

    public function render($file = null)
    {
        if ($file !== null)
        {
            $this->set_filename($file);
        }

        if (empty($this->_file))
        {
            throw new Exception("You must set a file.");
        }
        return self::capture($this->_file, $this->_data);
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->_data))
        {
            return $this->_data[$key];
        }
        else
        {
            throw new Exception('View variable is not set: '.$key);
        }
    }

    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    public function set($key, $value = null)
    {
        if (is_array($key))
        {
            foreach ($key as $name => $value)
            {
                $this->_data[$name] = $value;
            }
        }
        else
        {
            $this->_data[$key] = $value;
        }

        return $this;
    }
}
