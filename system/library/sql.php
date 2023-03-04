<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

    class mysql
    {
        var $sql_id = false;
        var $sql_connect = false;
        var $query = false;
        var $query_id = false;
        var $mysqlerror = '';

        public function connect_mysql($c, $u, $p, $n)
        {
            if(!$this->sql_id = @new mysqli($c, $u, $p, $n))
            {
                if(!ERROR_DATABASE)
                    return NULL;

                $this->out_error(mysqli_connect_error());
            }

            mysqli_query($this->sql_id, "/*!40101 SET NAMES 'utf8' */");

            $this->sql_connect = true;

            return NULL;
        }

        public function query($query)
        {
            if(!$this->sql_connect)
                $this->connect_mysql(CONNECT_DATABASE, USER_DATABASE, PASSWORD_DATABASE, NAME_DATABASE);

            if(!($this->query_id = mysqli_query($this->sql_id, $query)) and (mysqli_error($this->sql_id) and ERROR_DATABASE))
                $this->out_error(mysqli_error($this->sql_id), $query);

            return $this->query_id;
        }

        public function get($query_id = false)
        {
            if(!$query_id)
                $query_id = $this->query_id;

			$get = mysqli_fetch_assoc($query_id);

            return $get;
        }

        public function num($query_id = false)
        {
            if(!$query_id)
                $query_id = $this->query_id;

            return mysqli_num_rows($query_id);
        }

        public function id()
        {
            return mysqli_insert_id($this->sql_id);
        }

        public function esc()
        {
            mysqli_close($this->query_id);
            mysqli_stmt_close($this->sql_id);
        }

        private function out_error($error, $query = '')
        {
			global $go;

			if($go)
				sys::outjs(array('e' =>  'Query: '.$query.'<br>Error:<br>'.$error));

            if($query != '')
                echo 'Query: '.$query.'<br>';

            echo 'Error:<br>'.$error;

            exit();
        }
    }

    $sql = new mysql;
?>