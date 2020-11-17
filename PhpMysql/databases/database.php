<?php

/* *
 * This class contains the necessary requirements 
 * for database and database connection
 * */

abstract class Database{
    
    public $db;
    
    private $host;
    
    private $user;
    
    private $pass;
    
    private $admail;
    
    protected static $connection;
    
    
    public function __construct(){

        $this->host     = HOST;
        
        $this->db       = DATABASE;
        
        $this->user     = USERNAME;
        
        $this->pass     = PASSWORD;
        
        $this->admail   = ADMIN_EMAIL;
        
        $this->connect();
    }
    
    
    /* *
     * Get the database host name
     * */
     public function get_host(){
         return $this->host;
     }

     
    /* *
     * get the name of the database
     * */
    public function get_database(){
        return $this->db;
    }
    
    
    /* *
     * get the name of the user
     * */
    public function get_user(){
        return $this->user;
    }
    
    /* *
     * Get the email of the user
     * */
    public function get_admin_email(){
        return $this->admail;
    }
    
    
    /* *
     * connect to the database
     * */
     public function connect(){
         try{
                    $c  = mysqli_connect($this->host, $this->user, $this->pass, $this->db);
            }catch(Exception $e){
                     $c     = mysqli_connect($this->host, $this->user, $this->pass);
                     $retry = '';

                     if($c){
                         $this->create_database($this->db, $c);
                         $retry = $this->reconnect($this->db);
                         
                     }

                     self::$connection = is_object($retry)? $retry : 'Not connected';

                     if(!is_object(self::$connection)){
                         return new Exception("Connection Error!  Not Establish Valid Connection!");
                     }else{
                        return self::$connection;
                     }
         }
         
         self::$connection = $c;
         
         return self::$connection;
         
     }


     /* *
      * create database, especially if it doesnt exist
      * */
     public function create_database($db = '', $con = ''){
        if(empty($db)){
            $db = $this->db;
        }
        if(empty($con)){
            $con = self::$connection;
        }
        if(empty($con) || empty($db)){
            return false;
        }

        $con->query("CREATE DATABASE IF NOT EXISTS $db;");

     }
     
     
     /* *
      * Get the connection object
      * */
      public function get_connection(){
          if(!is_object(self::$connection)){
              $this->connect();
          }
          return self::$connection;
      }
    
    
    /* *
     * Try re-establish connection if it wasn't successful
     * */
     private function reconnect($db = ''){
         if(empty($db)){
             $db = $this->db;
         }
         $c  = mysqli_connect($this->host, $this->user, $this->pass, $db);
         if(is_object($c)){
             return $c;
         }else{
             return false;
         }
     }
      
}
