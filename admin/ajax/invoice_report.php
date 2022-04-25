<?php
session_start();
//include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');

//protect('admin');

class SSP {

    /**

     * Create the data output array for the DataTables rows

     *

     *  @param  array $columns Column information array

     *  @param  array $data    Data from the SQL get

     *  @return array          Formatted data in a row based format

     */

    static function data_output ( $columns, $data )

    {

        $out = array();

        for ( $i=0, $ien=count($data) ; $i<$ien ; $i++ ) {

            $row = array();

            for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ ) {

                $column = $columns[$j];

                // Is there a formatter?

                if ( isset( $column['formatter'] ) ) {

                    $row[ $column['dt'] ] = $column['formatter']( $data[$i][ $column['db'] ], $data[$i] );

                }

                else {

                    $row[ $column['dt'] ] = $data[$i][ $columns[$j]['db'] ];

                }

            }

            $out[] = $row;

        }

        return $out;

    }

    /**

     * Database connection

     *

     * Obtain an PHP PDO connection from a connection details array

     *

     *  @param  array $conn SQL connection details. The array should have

     *    the following properties

     *     * host - host name

     *     * db   - database name

     *     * user - user name

     *     * pass - user password

     *  @return resource PDO connection

     */

    static function db ( $conn )

    {

        if ( is_array( $conn ) ) {

            return self::sql_connect( $conn );

        }

        return $conn;

    }

    /**

     * Paging

     *

     * Construct the LIMIT clause for server-side processing SQL query

     *

     *  @param  array $request Data sent to server by DataTables

     *  @param  array $columns Column information array

     *  @return string SQL limit clause

     */

    static function limit ( $request, $columns )

    {

        $limit = '';

        if ( isset($request['start']) && $request['length'] != -1 ) {

            $limit = "LIMIT ".intval($request['start']).", ".intval($request['length']);

        }

        return $limit;

    }

    /**

     * Ordering

     *

     * Construct the ORDER BY clause for server-side processing SQL query

     *

     *  @param  array $request Data sent to server by DataTables

     *  @param  array $columns Column information array

     *  @return string SQL order by clause

     */

    static function order ( $request, $columns )

    {

        $order = '';

        if ( isset($request['order']) && count($request['order']) ) {

            $orderBy = array();

            $dtColumns = self::pluck( $columns, 'dt' );

            for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {

                // Convert the column index into the column data property

                $columnIdx = intval($request['order'][$i]['column']);

                $requestColumn = $request['columns'][$columnIdx];

                $columnIdx = array_search( $requestColumn['data'], $dtColumns );

                $column = $columns[ $columnIdx ];

                if ( $requestColumn['orderable'] == 'true' ) {

                    $dir = $request['order'][$i]['dir'] === 'asc' ?

                        'ASC' :

                        'DESC';

                    $orderBy[] = '`'.$column['db'].'` '.$dir;

                }

            }

            $order = 'ORDER BY '.implode(', ', $orderBy);

        }

        return $order;

    }

    /**

     * Searching / Filtering

     *

     * Construct the WHERE clause for server-side processing SQL query.

     *

     * NOTE this does not match the built-in DataTables filtering which does it

     * word by word on any field. It's possible to do here performance on large

     * databases would be very poor

     *

     *  @param  array $request Data sent to server by DataTables

     *  @param  array $columns Column information array

     *  @param  array $bindings Array of values for PDO bindings, used in the

     *    sql_exec() function

     *  @return string SQL where clause

     */

    static function filter ( $request, $columns, &$bindings )

    {

        $globalSearch = array();

        $columnSearch = array();

        $dtColumns = self::pluck( $columns, 'dt' );

        if ( isset($request['search']) && $request['search']['value'] != '' ) {

            $str = $request['search']['value'];

            for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {

                $requestColumn = $request['columns'][$i];

                $columnIdx = array_search( $requestColumn['data'], $dtColumns );

                $column = $columns[ $columnIdx ];

                if ( $requestColumn['searchable'] == 'true' ) {

                    $binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );

                    $globalSearch[] = "`".$column['db']."` LIKE ".$binding;

                }

            }

        }

        // Individual column filtering

        if ( isset( $request['columns'] ) ) {

            for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {

                $requestColumn = $request['columns'][$i];

                $columnIdx = array_search( $requestColumn['data'], $dtColumns );

                $column = $columns[ $columnIdx ];

                $str = $requestColumn['search']['value'];

                if ( $requestColumn['searchable'] == 'true' &&

                    $str != '' ) {

                    $binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );

                    $columnSearch[] = "`".$column['db']."` LIKE ".$binding;

                }

            }

        }

        // Combine the filters into a single string

        $where = '';

        if ( count( $globalSearch ) ) {

            $where = '('.implode(' OR ', $globalSearch).')';

        }

        if ( count( $columnSearch ) ) {

            $where = $where === '' ?

                implode(' AND ', $columnSearch) :

                $where .' AND '. implode(' AND ', $columnSearch);

        }

        if ( $where !== '' ) {

            $where = 'WHERE '.$where;

        }

        return $where;

    }

    /**

     * Perform the SQL queries needed for an server-side processing requested,

     * utilising the helper functions of this class, limit(), order() and

     * filter() among others. The returned array is ready to be encoded as JSON

     * in response to an SSP request, or can be modified if needed before

     * sending back to the client.

     *

     *  @param  array $request Data sent to server by DataTables

     *  @param  array|PDO $conn PDO connection resource or connection parameters array

     *  @param  string $table SQL table to query

     *  @param  string $primaryKey Primary key of the table

     *  @param  array $columns Column information array

     *  @return array          Server-side processing response array

     */

    static function simple ( $request, $conn, $table, $primaryKey, $columns )

    {

        $bindings = array();

        $db = self::db( $conn );

        // Build the SQL query string from the request

        $limit = self::limit( $request, $columns );

        $order = self::order( $request, $columns );

        $where = self::filter( $request, $columns, $bindings );

        // Main query to actually get the data

        $data = self::sql_exec( $db, $bindings,

            "SELECT `".implode("`, `", self::pluck($columns, 'db'))."`

			 FROM `$table`

			 $where

			 $order

			 $limit"

        );

        // Data set length after filtering

        $resFilterLength = self::sql_exec( $db, $bindings,

            "SELECT COUNT(`{$primaryKey}`)

			 FROM   `$table`

			 $where"

        );

        $recordsFiltered = $resFilterLength[0][0];

        // Total data set length

        $resTotalLength = self::sql_exec( $db,

            "SELECT COUNT(`{$primaryKey}`)

			 FROM   `$table`"

        );

        $recordsTotal = $resTotalLength[0][0];

        /*

         * Output

         */

        return array(

            "draw"            => isset ( $request['draw'] ) ?

                intval( $request['draw'] ) :

                0,

            "recordsTotal"    => intval( $recordsTotal ),

            "recordsFiltered" => intval( $recordsFiltered ),

            "data"            => self::data_output( $columns, $data )

        );

    }

    /**

     * The difference between this method and the `simple` one, is that you can

     * apply additional `where` conditions to the SQL queries. These can be in

     * one of two forms:

     *

     * * 'Result condition' - This is applied to the result set, but not the

     *   overall paging information query - i.e. it will not effect the number

     *   of records that a user sees they can have access to. This should be

     *   used when you want apply a filtering condition that the user has sent.

     * * 'All condition' - This is applied to all queries that are made and

     *   reduces the number of records that the user can access. This should be

     *   used in conditions where you don't want the user to ever have access to

     *   particular records (for example, restricting by a login id).

     *

     *  @param  array $request Data sent to server by DataTables

     *  @param  array|PDO $conn PDO connection resource or connection parameters array

     *  @param  string $table SQL table to query

     *  @param  string $primaryKey Primary key of the table

     *  @param  array $columns Column information array

     *  @param  string $whereResult WHERE condition to apply to the result set

     *  @param  string $whereAll WHERE condition to apply to all queries

     *  @return array          Server-side processing response array

     */

    static function complex ( $request, $conn, $table, $primaryKey, $columns, $whereResult=null, $whereAll=null )

    {

        $bindings = array();

        $db = self::db( $conn );

        $localWhereResult = array();

        $localWhereAll = array();

        $whereAllSql = '';

        // Build the SQL query string from the request

        $limit = self::limit( $request, $columns );

        $order = self::order( $request, $columns );

        $where = self::filter( $request, $columns, $bindings );

        $whereResult = self::_flatten( $whereResult );

        $whereAll = self::_flatten( $whereAll );

        if ( $whereResult ) {

            $where = $where ?

                $where .' AND '.$whereResult :

                'WHERE '.$whereResult;

        }

        if ( $whereAll ) {

            $where = $where ?

                $where .' AND '.$whereAll :

                'WHERE '.$whereAll;

            $whereAllSql = 'WHERE '.$whereAll;

        }

//echo $where;die;

        // Main query to actually get the data

        $data = self::sql_exec( $db, $bindings,

            "SELECT concat(login_users.first_name, ' ', login_users.last_name, ' (', login_users.username, ')') as created_by, invoice.reclamation, invoice.insurer, invoice.company, invoice.f_name, 

                    invoice.l_name, invoice.date, invoice.total, invoice.balance, invoice.rental_car, 

                    invoice.payment_status, invoice.id, invoice.sub_total, invoice.tps, invoice.tvq, invoice.franchise, 

                    invoice.total, invoice.deposit, invoice.balance, login_users.user_id

			 FROM `$table` LEFT JOIN login_users on (invoice.created_by = login_users.user_id)

			 $where

			 $order

			 $limit"

        );

        // Data set length after filtering

        $resFilterLength = self::sql_exec( $db, $bindings,

            "SELECT COUNT(`{$primaryKey}`)

			 FROM   `$table`

			 $where"

        );

        $recordsFiltered = $resFilterLength[0][0];

        // Total data set length

        $resTotalLength = self::sql_exec( $db, $bindings,

            "SELECT COUNT(`{$primaryKey}`)

			 FROM   `$table` ".

            $whereAllSql

        );

        $recordsTotal = $resTotalLength[0][0];



        $sub_total = 0;

        $tvq = 0;

        $tps = 0;

        $franchise = 0;

        $total = 0;

        $deposit = 0;

        $balance = 0;

        if(!empty($data)){

            $i=0;

            foreach ($data as $row){

                $data[$i]['created_by'] = '<a href="/admin/user.php?user_id='.$data[$i]['user_id'].'">'.$data[$i]['created_by'].'</a>';

                $sub_total += (int)$row['sub_total'];

                $tvq += $row['tvq'];

                $tps += $row['tps'];

                $franchise += $row['franchise'];

                $total += $row['total'];

                $deposit += $row['deposit'];

                $balance += $row['balance'];

                $i++;

            }

        }



        /*

         * Output

         */

        return array(

            "draw"            => isset ( $request['draw'] ) ?

                intval( $request['draw'] ) :

                0,

            "recordsTotal"    => intval( $recordsTotal ),

            "recordsFiltered" => intval( $recordsFiltered ),

            "data"            => self::data_output( $columns, $data ),

            "sub_total" => $sub_total,

            "tvq" => $tvq,

            "tps" => $tps,

            "franchise" => $franchise,

            "total" => $total,

            "franchise" => $franchise,

            "deposit" => $deposit,

            "balance" => $balance

        );

    }

    /**

     * Connect to the database

     *

     * @param  array $sql_details SQL server connection details array, with the

     *   properties:

     *     * host - host name

     *     * db   - database name

     *     * user - user name

     *     * pass - user password

     * @return resource Database connection handle

     */

    static function sql_connect ( $sql_details )

    {

        try {

            $db = @new PDO(

                "mysql:host={$sql_details['host']};dbname={$sql_details['db']}",

                $sql_details['user'],

                $sql_details['pass'],

                array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", )

            );

        }

        catch (PDOException $e) {

            self::fatal(

                "An error occurred while connecting to the database. ".

                "The error reported by the server was: ".$e->getMessage()

            );

        }

        return $db;

    }

    /**

     * Execute an SQL query on the database

     *

     * @param  resource $db  Database handler

     * @param  array    $bindings Array of PDO binding values from bind() to be

     *   used for safely escaping strings. Note that this can be given as the

     *   SQL query string if no bindings are required.

     * @param  string   $sql SQL query to execute.

     * @return array         Result from the query (all rows)

     */

    static function sql_exec ( $db, $bindings, $sql=null )

    {

        // Argument shifting

        if ( $sql === null ) {

            $sql = $bindings;

        }

        $stmt = $db->prepare( $sql );

        //echo $sql;

        // Bind parameters

        if ( is_array( $bindings ) ) {

            for ( $i=0, $ien=count($bindings) ; $i<$ien ; $i++ ) {

                $binding = $bindings[$i];

                $stmt->bindValue( $binding['key'], $binding['val'], $binding['type'] );

            }

        }

        // Execute

        try {

            $stmt->execute();

        }

        catch (PDOException $e) {

            self::fatal( "An SQL error occurred: ".$e->getMessage() );

        }

        // Return all

        return $stmt->fetchAll( PDO::FETCH_BOTH );

    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

     * Internal methods

     */

    /**

     * Throw a fatal error.

     *

     * This writes out an error message in a JSON string which DataTables will

     * see and show to the user in the browser.

     *

     * @param  string $msg Message to send to the client

     */

    static function fatal ( $msg )

    {

        echo json_encode( array(

            "error" => $msg

        ) );

        exit(0);

    }

    /**

     * Create a PDO binding key which can be used for escaping variables safely

     * when executing a query with sql_exec()

     *

     * @param  array &$a    Array of bindings

     * @param  *      $val  Value to bind

     * @param  int    $type PDO field type

     * @return string       Bound key to be used in the SQL where this parameter

     *   would be used.

     */

    static function bind ( &$a, $val, $type )

    {

        $key = ':binding_'.count( $a );

        $a[] = array(

            'key' => $key,

            'val' => $val,

            'type' => $type

        );

        return $key;

    }

    /**

     * Pull a particular property from each assoc. array in a numeric array,

     * returning and array of the property values from each item.

     *

     *  @param  array  $a    Array to get data from

     *  @param  string $prop Property to read

     *  @return array        Array of property values

     */

    static function pluck ( $a, $prop )

    {

        $out = array();

        for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {

            $out[] = $a[$i][$prop];

        }

        return $out;

    }

    /**

     * Return a string from an array or a string

     *

     * @param  array|string $a Array to join

     * @param  string $join Glue for the concatenation

     * @return string Joined string

     */

    static function _flatten ( $a, $join = ' AND ' )

    {

        if ( ! $a ) {

            return '';

        }

        else if ( $a && is_array($a) ) {

            return implode( $join, $a );

        }

        return $a;

    }

}



// DB table to use

$table = 'invoice';



// Table's primary key

$primaryKey = 'id';



// Array of database columns which should be read and sent back to DataTables.

// The `db` parameter represents the column name in the database, while the `dt`

// parameter represents the DataTables column identifier. In this case simple

// indexes

$columns = array(

    array( 'db' => 'created_by',     'dt' => 0 ),

    array( 'db' => 'reclamation',     'dt' => 1 ),

    array( 'db' => 'insurer',     'dt' => 2 ),

    array( 'db' => 'company',       'dt' => 3 ),

    array( 'db' => 'f_name',     'dt' => 4 ),

    array( 'db' => 'l_name',   'dt' => 5 ),

    array( 'db' => 'date',      'dt' => 6 ),

    array( 'db' => 'total',      'dt' => 7 ),

    array( 'db' => 'balance',      'dt' => 8 ),

    array( 'db' => 'rental_car',      'dt' => 9 ),

    array( 'db' => 'payment_status',      'dt' => 10 ),

    array( 'db' => 'sub_total', 'dt' => 12 ),

    array( 'db' => 'tps', 'dt' => 12 ),

    array( 'db' => 'tvq', 'dt' => 12 ),

    array( 'db' => 'franchise', 'dt' => 12 ),

    array( 'db' => 'total', 'dt' => 12 ),

    array( 'db' => 'deposit', 'dt' => 12 ),

    array( 'db' => 'balance', 'dt' => 12 ),

//    array( 'db' => 'clientid', 'dt' => 12 ),

    array( 'db' => 'id',  'dt' => 11, 'formatter' => function( $d, $row ) {

        return '<a title="Edit" href="main.php?invoice_id='.$d.'"><i class="glyphicon glyphicon-edit"></i></a> | <a href="invoice_delete.php?invoice_id='.$d.'" title="Remove" onclick="return confirm(\'Are you sure you want to delete this item?\');"><i class="glyphicon glyphicon-remove"></i></a> | <a target="_blank" title="Print" href="/admin/invoice/index.php?invoice_id='.$d.'&print=y"><i class="glyphicon glyphicon-print"></i></a>';

    } ),

);

function pushToFilter($array, $field, $value, $operator = '=') {
    array_push($array, sprintf("%s %s '%s'", $field, $operator, $value));
    return $array;
}

include_once __DIR__ . '/../../config.php';



// SQL server connection information

$sql_details = array(

    'user' => $dbUser,

    'pass' => $dbPass,

    'db'   => $dbName,

    'host' => $host,

);

$whereResult = array();
$whereAll = array();

//echo json_encode($_POST['client_id']); exit;

$filter_data = !empty($_GET['filter_data']) ? json_decode($_GET['filter_data']) : [];

foreach ($filter_data as $field => $value) {

    if ( ! trim(strlen($value)) ) continue;

    switch ($field) {
        case 'start_date':
            $value = date('Y-m-d', strtotime($value));
            $fieldName = 'date';
            $operator = '>=';
            break;
        case 'end_date':
            $value = date('Y-m-d', strtotime($value));
            $fieldName = 'date';
            $operator = '<=';
            break;
        case 'solde':
            $value = 0;
            $fieldName = 'balance';
            $operator = trim($value) == 'solde' ? '>' : '<=';
            break;
        case 'rental_car':
            $value = trim($value) == 'Yes' ? '1' : '0';
            $fieldName = 'rental_car';
            $operator = '=';
            break;
        case 'pending_paid':
            $value = trim($value) == 'Yes' ? '1' : '0';
            $fieldName = 'payment_status';
            $operator = '=';
            break;
        default:
            $value = trim($value);
            $fieldName = $field;
            $operator = '=';
            break;
    }

    $whereResult = pushToFilter($whereResult, $fieldName, $value, $operator);
    //$whereAll = pushToFilter($whereAll, $fieldName, $value, $operator);
}

/*
if(!empty($filter_data->start_date)){
    array_push($whereResult, 'date >= "'.date('Y-m-d', strtotime($filter_data->start_date)).'"');
    array_push($whereAll, 'date >= "'.date('Y-m-d', strtotime($filter_data->start_date)).'"');
}

if(!empty($filter_data->end_date)){
    array_push($whereResult, 'date <= "'.date('Y-m-d', strtotime($filter_data->end_date)).'"');
    array_push($whereAll, 'date <= "'.date('Y-m-d', strtotime($filter_data->end_date)).'"');
}

if(!empty($filter_data->tech)){
    array_push($whereResult, 'tech="'.$filter_data->tech.'"');
    array_push($whereAll, 'tech="'.$filter_data->tech.'"');
}

if(!empty($filter_data->insurer)){
    array_push($whereResult, 'insurer="'.$filter_data->insurer.'"');
    array_push($whereAll, 'insurer="'.$filter_data->insurer.'"');
}

if(!empty($filter_data->client_id)){
    array_push($whereResult, 'client_id='.$filter_data->client_id);
    array_push($whereAll,'client_id='.$filter_data->client_id);
}

if(!empty($filter_data->solde)){

    if($filter_data->solde == 'solde'){
        array_push($whereResult, 'balance > 0');
        array_push($whereAll,'balance > 0');
    } else {
        array_push($whereResult, 'balance = 0');
        array_push($whereAll,'balance = 0');
    }
}

if(!empty($filter_data->rental_car) && $filter_data->rental_car == 'Yes') {
    array_push($whereResult, 'rental_car=1');
    array_push($whereAll, 'rental_car=1');
}elseif(!empty($filter_data->rental_car) && $filter_data->rental_car == 'No'){
    array_push($whereResult, 'rental_car=0');
    array_push($whereAll, 'rental_car=0');
}

if(!empty($filter_data->pending_paid) && $filter_data->pending_paid == 'Yes'){
    array_push($whereResult, 'payment_status=1');
    array_push($whereAll, 'payment_status=1');
} elseif(!empty($filter_data->pending_paid) && $filter_data->pending_paid == 'No'){
    array_push($whereResult, 'payment_status=0');
    array_push($whereAll, 'payment_status=0');
}
*/

array_push($whereResult, 'confirm_invoice=1');
//array_push($whereAll, 'confirm_invoice=1');

array_push($whereResult, 'invoice_type="invoice"');
//array_push($whereAll, 'invoice_type="invoice"');

if ( isset($_SESSION['jigowatt']['user_level']) && $_SESSION['jigowatt']['user_level'][0] == 4 ) {
    array_push($whereResult, 'tech="'.$_SESSION['jigowatt']['user_id'].'"');
}

if ($filter_data) {
//    var_export($whereResult);die;
}

echo json_encode(
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, $whereResult, $whereAll )
);