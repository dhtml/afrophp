<?php
/**
* model class
*
*/

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model Class
 *
 */
class Model extends  \System\Base\Prototype
{

/**
* Select the database connection from the group names defined inside the database.php configuration file or an
* array.
*/
private $db_connected = false;


/** @var null
 * Sets table name
 */
public $table = null;

/** @var string
* stores the primary key of the table
*/
public $primary_key = 'id';

/** @var boolean
* checks if soft deletes are enabled
*/
public $soft_deletes = false;


    /**
     * Class constructor
     *
     * @return	void
     */
    public function __construct()
    {
        if ($this->table!=null) {
            $this->setup_table();
        }
    }

    /**
    *  Checks whether the current table exists in the current database.
    *
    *
    *  @return boolean             Returns TRUE if table given as argument exists in the database or FALSE if not.
    */
    public function table_exists()
    {
        return $this->table==null ? null: $this->db->table_exists($this->table);
    }


    /**
    *  Shorthand for inserting multiple rows from a json url
    *
    *
    *  @param  string   $url            The url or local file resource to load data from
    *
    *
    *  @return boolean                 Returns TRUE on success of FALSE on error.
    *
    */
    public function insert_json_url($url)
    {
        return $this->table==null ? null: $this->db->insert_json_url($this->table, $url);
    }

    /**
    *  Shorthand for inserting multiple rows from a json string
    *
    *
    *  @param  string  $json          The json array string
    *
    *
    *  @return boolean                 Returns TRUE on success of FALSE on error.
    *
    */
    public function insert_json_string($json)
    {
        return $this->table==null ? null: $this->db->insert_json_string($this->table, $json);
    }



            /**
         *  Returns one or more columns from ONE row of a table.
         *
         *  @param  string  $column         One or more columns to return data from.
         *
         *  @param  string  $where          (Optional) A MySQL WHERE clause (without the WHERE keyword).
         *
         *  @param  array   $replacements   (Optional) An array with as many items as the total parameter markers.
         *
         *
         *  @return mixed
         *
         */
            public function dlookup($column, $where = '', $replacements = '')
            {
                return $this->table==null ? null: $this->db->dlookup($column, $this->table, $where, $replacements);
            }


        /**
    	* Count number of entries
    	*/
        public function count_all()
        {
            return $this->db->count_all($this->table);
        }



         /**
         * insert data
         */
         public function insert($value = array())
         {
             return $this->db->insert($this->table, $value);
         }

         /**
         * insert data
         */
         public function replace($value = array())
         {
             return $this->db->replace($this->table, $value);
         }

    /**
    * setup_table
    *
    * preconfigure the table by prefixing, and making sure it exists
    *
    * @return object
    */
    public function setup_table()
    {
        //create table if it does not exist
            if (!$this->db->table_exists($this->table)) {
                $this->create_schema();
            }

            //get fields
            $all_fields=$this->list_fields();

            $default_fields=array('created_at','created_by','updated_at','updated_by','deleted_at','deleted_by');

            $missing_fields=array_diff($default_fields,$all_fields);

            //create default fields if they do not exist
            foreach($missing_fields as $field) {
              $sql="ALTER TABLE `{$this->table}` ADD `$field` INT NOT NULL DEFAULT '0';";
              $this->db->query($sql);
            }

        return $this;
    }

    public function list_fields()
    {
      return $this->db->list_fields($this->table);
    }

    /**
    * Creation of schema, this function is meant to be override by the model
    *
    * Create database schema
    *
    * @return mixed
    */
    public function create_schema()
    {
        $args = func_get_args();
        if ($this->table==null || empty($args)) {
            return;
        }

        $schema = isset($args[0]) ? $args[0] : null;
        if (isset($args[1]) && strtolower($args[1])=='myisam') {
            $engine='MyISAM';
        } else {
            $engine='InnoDB';
        }

        $char_set=config_item('dbase_char_set', 'uft8');


        $sql="
       CREATE TABLE IF NOT EXISTS `{$this->table}` (
         $schema
       ) ENGINE=$engine DEFAULT CHARSET=$char_set;
       ";

        return $this->db->query($sql);
    }
}
