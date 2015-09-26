<?php

class db{
    private static $_instance; //自身对象容器
    public static $dbConfig = array(
        'host'=>'localhost',
        'user'=>'root',
        'pwd'=>'*****',
        'dbname'=>'db_etta',
    ); //数据库连接参数
    public static $connSource; //数据库连接句柄

    private function __construct(){}
    private function __clone(){}

    //实例化自身
    static public function get_instance(){
        if(!static::$_instance instanceof self){
            static::$_instance = new self();
        }
        return static::$_instance;
    }

    //连接数据库方法
    public function connect(){
        if(!isset(static::$connSource)){
            static::$connSource = mysql_connect(self::$dbConfig['host'],self::$dbConfig['user'],self::$dbConfig['pwd']);
            if(!isset(static::$connSource)){
                throw new Exception('mysql connect error:'.mysql_error());
            }
            else{
                mysql_select_db(self::$dbConfig['dbname'],static::$connSource);
                mysql_query('set names utf8');
            }
        }
        return static::$connSource;
    }
}

function get_area_json($pid=0)
{
    $conn = db::get_instance()->connect();
    static $area;
    $sql = "select `area_id`,`area_name`,`parent_id` from `tb_area` where `parent_id`={$pid} order by `area_id` asc";
    $result = mysql_query($sql,$conn);
    if(mysql_num_rows($result)>0){
        while($row = mysql_fetch_assoc($result)){
            $area[] = $row;
            get_area_json($row['area_id']);
        }
    }
    return $area;
}
echo json_encode(get_area_json());

