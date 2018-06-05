<?php
	/**
	* Methods:  
	*   
	*   db_dql(): SELECT、FROM、WHERE
	* 	db_dml(): INSERT、UPDATE、DELETE
	*	db_prepare(): prepare statment
	*		+---eg: db_pre(@mysql_sentence, @mode:"isiss", @"...", @"...", @"...", ...);
	*		+---return: 1(success) 2(error)
	*
	*	db_match(): match data;
	*		+---eg: db_match(@table, @list_item, @key_words);
	*		+---return:	1(match) 2(dont match);
	*		
	*	db_find(): find key row;
	*		+---eg: db_find(@table, @list_item, @key_words, @mode=0 or 1);
	*		+		@mode: 0[default] return first row;
	*		+ 			   1 return all rows;
	*		+---return:	array[];
	*
	*	db_all(): print a table;
	*		+---eg: db_find(@table);
	*		+---return:	two-dimensional array[];
	*
	*
	*
	*
	*
	* Transform into
	*	
	*	into_table():Just test for show;
	*	into_json():Encoding into json;
	*
	*
	*
	*
	*/


	class db_class{
		private $mysqli;
		private $result;
		private static $db="mwx";
		private static $host="localhost:3306";
		private static $user="root";
		private static $pwd="123456";
	

		public function __construct(){
			//initialization
			$this->mysqli=new mysqli(self::$host,self::$user,self::$pwd,self::$db);
			if ($this->mysqli->connect_error) {
				die("error".$this->mysqli->connect_error);
			}
			$this->mysqli->query("set names utf8");

		}


		private function makeValuesReferenced($arr){
		    $refs = array();
		    foreach($arr as $key => $value)
		        $refs[$key] = &$arr[$key];
		    return $refs;
		}


		public function db_dql($sql){
			$res=$this->mysqli->query($sql) or die("excute error".$this->mysqli->error);
			return $res;
		}


		public function db_dml($sql){
			$res=$this->mysqli->query($sql) or die("excute error".$this->mysqli->error);
			return $res;			

			if (!res) {
				return 0;//fail
			}else{
				if($this->mysqli->affacted_rows>0){
					return 1;//success
				}else{
					return 2;//fail
				}
			}
		}

		public function db_prepare($sql,$mode,$para){
			$list=array();
			$args=func_get_args();
			for ($i=1; $i<count($args); $i++) { 
				array_push($list,$args[$i]);
			}

			$stmt=$this->mysqli->prepare($sql);
			$return = call_user_func_array(array($stmt, 'bind_param'), $this->makeValuesReferenced($list));
			$res=$stmt->execute();

		    if ($res){
		    	return 1;
		    }else{
		    	return 2;
		    }
		}	   


		public function db_match($tb,$item,$key){
			$sql="SELECT * from $tb WHERE $item='$key';";
			$result=$this->mysqli->query($sql);
			$row=$result->fetch_assoc();
			if ($row[$item]=="$key") {
				return 1;//match
			}else{
				return 2;//doesn't match
			}

			$result->free();	
		}


		public function db_find($tb,$item,$key,$mode=0){
			$sql="SELECT * from $tb WHERE $item='$key';";
			$result=$this->mysqli->query($sql);
			if ($mode==0) {
				$row=$result->fetch_row();
				return $this->result=$row;
			}else{
				while($row = $result->fetch_row()){
					$rows[] = $row;
				};				
				return $this->result=$rows;
			}				
			
			$result->free();
		}


		public function db_all($tb){
			$sql="SELECT * from $tb";
			$result=$this->mysqli->query($sql);
			while($row = $result->fetch_row()){
				$rows[] = $row;
			 };
			return $this->result=$rows;		
			$result->free();

		}













		public function into_table(){
			$arr=$this->result;
			echo "<style type='text/css'>tr:nth-child(2n+1){background-color:#ccc}</style>";
			echo "<table style='border-collapse:collapse;text-indent:15px;'>";			
			if (count($arr) == count($arr,1)) {
				echo "<tr>";			
				for ($i=0;$i<count($arr);$i++) {
					echo "<td>"."$arr[$i]"."</td>";
				}
				echo "</tr>";
			}else{
				for ($i=0; $i<count($arr);$i++){ 
					echo "<tr>";
					for ($j=0;$j<count($arr[$i]);$j++){
						echo "<td>".$arr[$i][$j]."</td>";
					}
					echo "</tr>";
				}
				echo "</table>";
			}
		}


		public function into_json(){
			$data=$this->result;
			header("content-type:application/json");
			header("Expires: -1");
			header("Cache_Control: no_cache");
			header("Pragma: no-cache");		 			
			$json = json_encode($data); 
			echo $json;
		}








	}

?>


