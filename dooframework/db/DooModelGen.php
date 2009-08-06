<?php
/**
 * DooModelGen class file.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */


/**
 * DooModelGen serves as a Model class file generator for rapid development
 *
 * <p>If you have your database configurations setup, call DooModelGen::gen_mysql() and
 * it will generate the Model files for all the tables in that database</p>
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @version $Id: DooModelGen.php 1000 2009-07-7 18:27:22
 * @package doo.db
 * @since 1.0
 */
class DooModelGen{
    /**
     * Generates Model class files from a MySQL database
     */
    public static function gen_mysql(){
        $dbconf = Doo::db()->getDefaultDbConfig();
        $dbname = $dbconf[1];

        echo "<html><head><title>DooPHP Model Generator - DB: $dbname</title></head><body bgcolor=\"#2e3436\">";

        $smt = Doo::db()->query("SHOW TABLES");
        $tables = $smt->fetchAll();
        foreach( $tables as $tbl ){
           $tblname = $tbl["Tables_in_$dbname"];

           $smt2 = Doo::db()->query("DESC `$tblname`");
           $fields = $smt2->fetchAll();

           $classname = '';
           $temptbl = $tblname;
           for($i=0;$i<strlen($temptbl);$i++){
               if($i==0){
                   $classname .= strtoupper($temptbl[0]);
               }
               else if($temptbl[$i]=='_' || $temptbl[$i]=='-' || $temptbl[$i]=='.' ){
                    $classname .= strtoupper( $temptbl[ ($i+1) ] );
                    $arr = str_split($temptbl);
                    array_splice($arr, $i, 1);
                    $temptbl = implode('', $arr);
               }else{
                    $classname .= $temptbl[$i];
               }
           }

           $filestr = "<?php\nclass $classname{\n";
           $pkey = '';
           $fieldnames = array();

           foreach($fields as $f){
                $filestr .= "    public \${$f['Field']};\n";
                $fieldnames[] = $f['Field'];
                if($f['Key']=='PRI'){
                    $pkey = $f['Field'];
                }
           }

           $fieldnames = implode($fieldnames, "','");
           $filestr .= "    public \$_table = '$tblname';\n";
           $filestr .= "    public \$_primarykey = '$pkey';\n";
           $filestr .= "    public \$_fields = array('$fieldnames');\n";
           $filestr .= "}\n?>";
           $handle = fopen(Doo::conf()->SITE_PATH . "/protected/model/$classname.php", 'w+');
           fwrite($handle, $filestr);
           fclose($handle);
           echo "<span style=\"font-size:190%;font-family: 'Courier New', Courier, monospace;\"><span style=\"color:#fff;\">Model for table </span><strong><span style=\"color:#e7c118;\">$tblname</span></strong><span style=\"color:#fff;\"> generated. File - </span><strong><span style=\"color:#729fbe;\">$classname</span></strong><span style=\"color:#fff;\">.php</span></span><br/><br/>";
        }

        $total = sizeof($tables);
        echo "<span style=\"font-size:190%;font-family: 'Courier New', Courier, monospace;color:#fff;\">Total $total file(s) generated.</span></body></html>";
    }
	
	public static function gen_pgsql(){
        $dbconf = Doo::db()->getDefaultDbConfig();
        $dbSchema = $dbconf[6];
        $dbname = $dbconf[1];

        echo "<html><head><title>DooPHP Model Generator - DB: $dbname</title></head><body bgcolor=\"#2e3436\">";

        $smt = Doo::db()->query("SELECT table_name as name FROM INFORMATION_SCHEMA.tables WHERE table_schema = '$dbSchema'");
        $tables = $smt->fetchAll();
        foreach( $tables as $tbl ){
            $tblname = $tbl["name"]; //tablename
            //Get table description
            $smt2 = Doo::db()->query(
                "SELECT DISTINCT column_name AS name, data_type AS type, is_nullable AS null,
                column_default AS default, ordinal_position AS position, character_maximum_length AS char_length,
                character_octet_length AS oct_length FROM information_schema.columns
                WHERE table_name = '$tblname' AND table_schema = '$dbSchema'   ORDER BY position"
            );
            $fields = $smt2->fetchAll();
            //Get primary key
            $smt3 = Doo::db()->query(
                "SELECT relname, indkey
                  FROM pg_class, pg_index
                 WHERE pg_class.oid = pg_index.indexrelid
                   AND pg_class.oid IN (
                    SELECT indexrelid
                      FROM pg_index, pg_class
                     WHERE pg_class.relname='$tblname'
                       AND pg_class.oid=pg_index.indrelid
                       AND indisprimary = 't')"); //indkey
            $fields3 = $smt3->fetchAll();
            $smt4 = Doo::db()->query(
                "SELECT t.relname, a.attname, a.attnum
                     FROM pg_index c
                LEFT JOIN pg_class t
                       ON c.indrelid  = t.oid
                LEFT JOIN pg_attribute a
                       ON a.attrelid = t.oid
                      AND a.attnum = ANY(indkey)
                    WHERE t.relname = '$tblname'
                      AND a.attnum = {$fields3[0]['indkey']}"

            );
            $fields4 = $smt4->fetchAll();
            $pkey = $fields4[0]['attname'];
            //Prepare model class
           $classname = '';
           $temptbl = $tblname;
           for($i=0;$i<strlen($temptbl);$i++){
               if($i==0){
                   $classname .= strtoupper($temptbl[0]);
               }
               else if($temptbl[$i]=='_' || $temptbl[$i]=='-' || $temptbl[$i]=='.' ){
                    $classname .= strtoupper( $temptbl[ ($i+1) ] );
                    $arr = str_split($temptbl);
                    array_splice($arr, $i, 1);
                    $temptbl = implode('', $arr);
               }else{
                    $classname .= $temptbl[$i];
               }
           }

           $filestr = "<?php\nclass $classname{\n";
		   
           $fieldnames = array();
           foreach($fields as $f){
                $filestr .= "    public \${$f['name']};\n";
                $fieldnames[] = $f['name'];
           }

           $fieldnames = implode($fieldnames, "','");
           $filestr .= "    public \$_table = '$tblname';\n";
           $filestr .= "    public \$_primarykey = '$pkey';\n";
           $filestr .= "    public \$_fields = array('$fieldnames');\n";
           $filestr .= "}\n?>";
           $handle = fopen(Doo::conf()->SITE_PATH . "/protected/model/$classname.php", 'w+');
           fwrite($handle, $filestr);
           fclose($handle);
           echo "<span style=\"font-size:190%;font-family: 'Courier New', Courier, monospace;\"><span style=\"color:#fff;\">Model for table </span><strong><span style=\"color:#e7c118;\">$tblname</span></strong><span style=\"color:#fff;\"> generated. File - </span><strong><span style=\"color:#729fbe;\">$classname</span></strong><span style=\"color:#fff;\">.php</span></span><br/><br/>";
        }

        $total = sizeof($tables);
        echo "<span style=\"font-size:190%;font-family: 'Courier New', Courier, monospace;color:#fff;\">Total $total file(s) generated.</span></body></html>";
    }
}

?>