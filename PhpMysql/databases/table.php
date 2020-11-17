<?php


/* *
 * Handle tables on their own
 * Extends from the main database class
 * */
class Table extends Database{
    public $table;
    
    public function __construct($table = '', $cols = []){
        parent::__construct();
        
        $this->table     = $table;
        
        if(empty($table) && empty($this->table)){
            $this->table = TABLE;
        }
        
        if(!empty($table) && !empty($cols)){
            $this->create_table($table, $cols);
        }
        
        
    }
    
    
    /* *
     * Create table given the colums
     * */
    public function create_table($name = '', $columns = []){
        
        if(empty($name)){
            $name = !empty($this->table)?$this->table : TABLE;
        }
        
        if(empty($name) || empty($columns) || !is_array($columns)){
            return false;
        }
        
        $cols    = "(";
        
        foreach($columns as $c=>$t){
            $cols .= "$c $t,";
        }
        
        $cols   = trim($cols, ",").")";
        $query  = "CREATE TABLE IF NOT EXISTS {$name}{$cols};";
        
        $this->run($query);
    }


    /* *
     * And now we wanna change table name
     * */
    public function rename( $what ){
        
        $result = $this->run("RENAME TABLE ".$this->table." TO $what");
    }

    /* *
     * What if we want to delete this table
     * */
    public function delete($table = ""){
        if(empty($table)){
            $table = $this->table;
        }

        if(!empty($table)){
            $this->run("DROP TABLE IF EXISTS {$table};");
            return true;
        }
        return false;
    }


    /* *
     * Get all the data in this table
     * */
    public function get_data(){
        return $this->run("SELECT * FROM ".$this->table.";")->fetch_all();
    }


    /* *
     * Get data as an object
     * */
    public function get_data_as_object(){
        return $this->run("SELECT * FROM ".$this->table.";")->fetch_fields();
    }

    /* *
     * Get single row as array
     * */
    public function get_object($n = '1', $v = '1'){
        return $this->run("SELECT * FROM ".$this->table." WHERE $n='$v';")->fetch_object();
    }

    /* *
     * And we want to clear the table
     * */
    public function clear(){
        return $this->run("DELETE FROM ".$this->table);
    }


    /* *
     * What if we want to add a column
     * */
    public function add_column($name, $desc='int', $after = ''){
        $table  = $this->table;

        if(empty($after)){
            $after = $this->last_column();
        }
        
        $q      = "ALTER TABLE $table ADD COLUMN $name $desc after {$after};";
        $this->run($q);
    }


    /* *
     * Remove an existing column
     * */
    public function remove_column($name){
        return $this->run("ALTER TABLE ".$this->table." DROP COLUMN $name");
    }

    /* *
     * Rename column
     * */
    public function rename_column($which, $what, $type = "text"){
        $this->run("ALTER TABLE ".$this->table." CHANGE $which $what $type");
    }

    /* *
     * Run the given query
     * */
    public function run($query){
        return self::$connection->query($query);
    }
    
    
    /* *
     * Escape spacial characters before entering into the database;
     * */
    public function uncrook($data = ''){
        
        if(is_string($data)){
            return self::$connection->real_escape_string($data);
        }
        
        if(is_array($data)){
            return array_map([self::$connection, "real_escape_string"], $data);
        }
    }

    /* *
     * Add the single quote to each item
     * */
    public function quote_all($data){
        return array_map(function($x){ return "'".$x."'";}, $data);
    }
    
    
    /* *
     * Insert data into the table
     * */
    public function insert($table = '', $data = []){
        
        $table      = addslashes($table);
        
        if(empty($table)){
            $table  = !empty($this->table)? $this->table : TABLE;
        }
        
        if(empty($table) || empty($data) || !is_array($data)){
            return false;
        }

        $data       = $this->uncrook($data);
        
        $q         = "INSERT INTO {$table}("; 
         
        $cols      = implode(",", array_keys($data));
        $vals      = implode(",", $this->quote_all(array_values($data)));

        
        $query     = $q.$cols.") VALUES(".$vals.");";

        print_r($this->last_column());

     }

     /* *
      * get the total number of rows in the table
      * */
     public function number_of_rows(){
        $rows   = $this->run("select count(*) from ".$this->table.";")->fetch_array();
        
        return end($data);
     }


    /* *
     * Get the last id inserted
     * */
    public function last_id(){
        return self::$connection->insert_id;
    }


    /* *
     * Get the name of the last column in mysql
     * */
    public function last_column($table = ''){

        $table = !empty( $table )? $table : $this->table;
        $dt    = $this->run("DESC $table")->fetch_all();

        return end( $dt )[0];
    }

    /* *
     * Get the number of the columns
     * */
    public function column_count($table = ''){

        $table = !empty( $table )? $table : $this->table;
        $dt    = $this->run("select * from $table")->field_count;

        return $dt;
    }

}





/* *
 * Public functions
 * */
 
function database(){
    return new Database();
}


/* *
 * Function to return the instance of the table
 * */
function table($name ='', $data = []){
    return new Table($name, $data);
}
