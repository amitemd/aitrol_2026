<?php  

function d($var,$a=false)
{
	  echo "<pre>";
	  print_r($var);
	  echo "</pre>";
	  if($a)exit;
}

function selectDatabase()
{
        $host = \Session::get('host');
        $db_user = \Session::get('db_user');
        $db_password = \Session::get('db_password');
        $db_name = \Session::get('db_name');
        
        if (!empty($host) && !empty($db_user) && !empty($db_name) ) {

            selectDatabase1($host,$db_user,$db_password,$db_name);
        }
}


function selectDatabase1($host,$db_user,$db_password,$db_name)
{
    
    \Config::set('database.connections.mysql.host', $host);
    \Config::set('database.connections.mysql.username', $db_user);
    \Config::set('database.connections.mysql.password', $db_password);
    \Config::set('database.connections.mysql.database', $db_name);

    \Config::set('database.default', 'mysql');
     \DB::reconnect('mysql');
}


function objectToArray($data)
{
    if (is_array($data) || is_object($data))
    {
        $result = array();
        foreach ($data as $key => $value)
        {
            $result[$key] = objectToArray($value);
        }
        return $result;
    }
    return $data;
}

function dbConnect($host,$db_user,$db_password,$db_name)
{
    error_reporting(0);
    $mysqli = new mysqli($host, $db_user, $db_password, $db_name);

    /* check if server is alive */
    if ($mysqli->ping()) {
        return true;
    } else {
        return false;
    }
    /* close connection */
    $mysqli->close();
}

function setDbConnect($db_id)
{
    $companyData = \DB::table('company')->where('company_id', $db_id)->first();
    $companyData = objectToArray($companyData);
        
    selectDatabase1($companyData['host'], $companyData['db_user'], $companyData['db_password'], $companyData['db_name']);
}


/*
 * Function to Encrypt user sensitive data for storing in the database
 *
 * @param string    $value      The text to be encrypted
 * @param           $encodeKey  The Key to use in the encryption
 * @return                      The encrypted text
 */
function encryptIt($value) {
    // The encodeKey MUST match the decodeKey
    $encodeKey = 'Li1KUqJ4tgX14dS,A9ejk?uwnXaNSD@fQ+!+D.f^`Jy';
    $encoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($encodeKey), $value, MCRYPT_MODE_CBC, md5(md5($encodeKey))));
    return($encoded);
}

/*
 * Function to decrypt user sensitive data for displaying to the user
 *
 * @param string    $value      The text to be decrypted
 * @param           $decodeKey  The Key to use for decryption
 * @return                      The decrypted text
 */
function decryptIt($value) {
    // The decodeKey MUST match the encodeKey
    $decodeKey = 'Li1KUqJ4tgX14dS,A9ejk?uwnXaNSD@fQ+!+D.f^`Jy';
    $decoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($decodeKey), base64_decode($value), MCRYPT_MODE_CBC, md5(md5($decodeKey))), "\0");
    return($decoded);
}

function emptyDatabase()
{
    foreach (\DB::select('SHOW TABLES') as $table) {
        $table_array = get_object_vars($table);
        
        if(!empty($table_array)) {
            \Schema::drop($table_array[key($table_array)]);
        }
    }
}

function uniqueMultidimArray($array, $key) { 
    $temp_array = array(); 
    $i = 0; 
    $key_array = array(); 
    
    foreach($array as $val) { 
        if (!in_array($val[$key], $key_array)) { 
            $key_array[$i] = $val[$key]; 
            $temp_array[$i] = $val; 
        } 
        $i++; 
    } 
    return $temp_array; 
}

function getDefaultLocation()
{
    $loc_id = \DB::table('location')->select('loc_code')->where('inactive', '=', 1)->first();
    
    return $loc_id->loc_code;
}

function AssColumn($a=array(), $column='id')
{
    $two_level = func_num_args() > 2 ? true : false;
    if ( $two_level ) $scolumn = func_get_arg(2);

    $ret = array(); settype($a, 'array');
    if ( false == $two_level )
    {   
        foreach( $a AS $one )
        {   
            if ( is_array($one) ) 
                $ret[ @$one[$column] ] = $one;
            else
                $ret[ @$one->$column ] = $one;
        }   
    }   
    else
    {   
        foreach( $a AS $one )
        {   
            if (is_array($one)) {
                if ( false==isset( $ret[ @$one[$column] ] ) ) {
                    $ret[ @$one[$column] ] = array();
                }
                $ret[ @$one[$column] ][ @$one[$scolumn] ] = $one;
            } else {
                if ( false==isset( $ret[ @$one->$column ] ) )
                    $ret[ @$one->$column ] = array();

                $ret[ @$one->$column ][ @$one->$scolumn ] = $one;
            }
        }
    }
    return $ret;
}

function formatDate($value)
{
    $pref = \DB::table('preference')->where('category', 'preference')->get();
    $prefData = AssColumn($a=$pref, $column='id');

    if($prefData[2]->value == '0') {
        //yyyy-mm-dd
        $format ='Y'.$prefData[3]->value.'m'.$prefData[3]->value.'d'; 
        $date = date($format, strtotime($value));
    }elseif($prefData[2]->value == '1') {
        //dd-mm-yyyy
        $format ='d'.$prefData[3]->value.'m'.$prefData[3]->value.'Y'; 
        $date = date($format, strtotime($value));
    }elseif($prefData[2]->value == '2') {
        //mm-dd-yyyy
        $format ='m'.$prefData[3]->value.'d'.$prefData[3]->value.'Y'; 
        $date = date($format, strtotime($value));
    }elseif($prefData[2]->value == '3') {
        //D-M-yyyy
        $format ='d'.$prefData[3]->value.'M'.$prefData[3]->value.'Y'; 
        $date = date($format, strtotime($value));
    }elseif($prefData[2]->value == '4') {
        //yyyy-mm-D
        $format ='Y'.$prefData[3]->value.'M'.$prefData[3]->value.'d'; 
        $date = date($format, strtotime($value));
    }

    return $date;

}

function DbDateFormat($value){
    $preference = \Session::get('date_format_type');
    $data = str_replace(['/','.',' ','-'],['-','-','-','-'],$preference);
    $data = explode('-', $data);
    $mm = $data[0];
    if($mm=='mm'){
        $dateInfo = str_replace(['/','.',' ','-'],['-','-','-','-'],$value);
        $datas = explode('-', $dateInfo);
        $month = $datas[0];
        $day = $datas[1];
        $year = $datas[2];
        $value = $day.'-'.$month.'-'.$year;
    }else{
       $value = str_replace(['/','.',' ','-'],['-','-','-','-'],$value);
   }
       $value = date('Y-m-d',strtotime($value));
    
     return $value;
}

function getDestinatin($loc)
{
    $location = \DB::table('location')
        ->where('loc_code', $loc)
        ->select('location_name')
        ->first();
    return $location->location_name;
}

function getSupplier($sid)
{
    $supplier = \DB::table('suppliers')
        ->where('supplier_id', $sid)
        ->select('supp_name')
        ->first();
    return $supplier->supp_name;
}

function getCustomer($cid){
    $customer = \DB::table('debtors_master')
        ->where('debtor_no', $cid)
        ->select('name')
        ->first();
    return $customer->name;
}

function getItemName($stock_id)
{
    $location = \DB::table('item_code')->where('stock_id', $stock_id)->select('description')->first();
    return $location->description;
}

function getItemQtyByLocationName($location_code,$stock_id)
{
    
        $qty = DB::table('stock_moves')
                            ->where(['loc_code'=>strtoupper($location_code),'stock_id'=>$stock_id])
                            ->sum('qty');
        if(empty($qty)){
            $qty = 0;
        }
        return $qty;
}

function getTransactionType($code)
{
    $type = '';
    if($code == PURCHINVOICE){
        $type = 'Stock In By Purchase';
    }
    elseif($code == SALESINVOICE){
        $type = 'Stock Out By Sale';
    }
    elseif($code == STOCKMOVEIN){
        $type = 'Stock In By Transfer';
    }
    elseif($code == STOCKMOVEOUT){
        $type = 'Stock Out By Transfer';
    }

        return $type;
}

function getShipmentStatus($sid)
{
    $info = \DB::table('shipment')->where('id', $sid)->select('status')->first();
    if($info->status == 1){
        $status = 'Delivered';
    }else{
       $status = 'Packed'; 
    }
    return $status;
}

function backup_tables($host,$user,$pass,$name,$tables = '*')
{
    try {
        $con = mysqli_connect($host,$user,$pass,$name);
    }catch(Exception $e){
        
    }
    //mysqli_select_db($name,$link);

    if (mysqli_connect_errno())
    {
        \Session::flash('fail', "Failed to connect to MySQL: ".mysqli_connect_error());
        return 0;
    }
    
    //get all of the tables
    if($tables == '*')
    {
        $tables = array();
        $result = mysqli_query($con, 'SHOW TABLES');
        while($row = mysqli_fetch_row($result))
        {
            $tables[] = $row[0];
        }
    }
    else
    {
        $tables = is_array($tables) ? $tables : explode(',',$tables);
    }
    
    //cycle through
    $return = '';
    foreach($tables as $table)
    {
        $result = mysqli_query($con, 'SELECT * FROM '.$table);
        $num_fields = mysqli_num_fields($result);
        
        
        //$return.= 'DROP TABLE '.$table.';';
        $row2 = mysqli_fetch_row(mysqli_query($con, 'SHOW CREATE TABLE '.$table));
        $return.= "\n\n".str_replace("CREATE TABLE", "CREATE TABLE IF NOT EXISTS", $row2[1]).";\n\n";
        
        for ($i = 0; $i < $num_fields; $i++) 
        {
            while($row = mysqli_fetch_row($result))
            {
                $return.= 'INSERT INTO '.$table.' VALUES(';
                for($j=0; $j < $num_fields; $j++) 
                {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = preg_replace("/\n/","\\n",$row[$j]);
                    if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                    if ($j < ($num_fields-1)) { $return.= ','; }
                }
                $return.= ");\n";
            }
        }

        $return.="\n\n\n";
    }
    
    $backup_name = date('Y-m-d-His').'.sql';
    //save file
    $handle = fopen(storage_path("laravel-backups").'/'.$backup_name,'w+');
    fwrite($handle,$return);
    fclose($handle);

    return $backup_name;
}

  function getAvailableQtyByLocation($stockid,$loc){
   $data = \DB::select("SELECT SUM(`qty`) as qty FROM `stock_moves` WHERE `loc_code`='$loc' AND `stock_id`= '$stockid'");
   if(!isset($data[0]->qty) || $data[0]->qty<=0){
    $qty = 0;
   }else{
    $qty = $data[0]->qty;
   }
   return $qty;
    }

function getTotalPaidAmountByOrder($order_reference,$order_no){
    $invoiceInfo = \DB::select("SELECT SUM(total) as invoiceAmount,SUM(paid_amount) as paidAmount FROM sales_orders WHERE order_reference_id = '$order_no'");
    $dueAmount = ($invoiceInfo[0]->invoiceAmount - $invoiceInfo[0]->paidAmount);
    //d($dueAmount,1);
    return $dueAmount;
}

function itemQtyInOrder($order_no,$receive_no,$itemCode){
    $orderedQty = \DB::table('purch_order_details')
                    ->where(['order_no'=>$order_no,'item_code'=>$itemCode])
                    ->sum('quantity_ordered');

    $receivedQty = \DB::table('receive_order_details')
                    ->where(['order_no'=>$order_no,'item_code'=>$itemCode])
                     ->sum('quantity');

    $currentQty = \DB::table('receive_order_details')
                    ->where(['receive_id'=>$receive_no,'item_code'=>$itemCode])
                     ->sum('quantity');

    $remaimQty = ($orderedQty-$receivedQty+$currentQty);            
    
    return $remaimQty;
}