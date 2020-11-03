<?php
namespace ReThumbAdvanced\ShortQ\DataProvider;
use ReThumbAdvanced\ShortQ\Item as Item;
use ReThumbAdvanced\ShortQ\ShortQ as ShortQ;


/* WP Mysql DataProvider
*
*/
class MysqlDataProvider implements DataProvider
{
   protected $qName; // Limit is 30 chars!
   protected $slug;  // Limit is 30 chars!

   protected $table;

   protected $query_size_limit = 10000;  // in strlen characters.

   /* Constructor */
   public function __construct($slug, $qName)
   {
      global $wpdb;
      $this->slug = $slug;
      $this->qName = $qName;

      $this->table = $wpdb->prefix . 'shortpixel_queue';
   }

   public function enqueue($items)
   {
      global $wpdb;
      if (! is_array($items))
        return false;
       // start higher to allow priority additions easily.
      $list_order = (10 + $this->itemCount());
      $now = date('Y-m-d H:i:s');

      $sql = 'INSERT IGNORE INTO ' . $this->table . ' (queue_name, plugin_slug, value, list_order, item_id, updated, created) VALUES ';
      $values = array();
      foreach ($items as $item)
      {
        $item_id = (int) $item->item_id;
        $value = $item->getRaw('value'); // value;

        $order = (! is_null($item->list_order)) ? $item->list_order : $list_order;

        $values[] = $wpdb->prepare('(%s, %s, %s, %d, %d, %s, %s)', $this->qName, $this->slug, $value, $order, $item_id, $now, $now);
        if (! isset($item->list_order))
          $list_order++;

      }
      $sql .= implode( ",\n", $values );
      $result = $wpdb->query($sql, $values);

      if (! $this->checkQueryOK())
      {
        return false;
      }

      return $result;
   }

   /* Check item consistency and check if items are not already in this queue. Must be unique */
   protected function prepareItems($items)
   {
      global $wpdb;

      $items_ids = array();
      foreach($items as $key => $item)
      {
         if (isset($item['id']))
          $items_ids[] = $item['id'];
        else // no id, no q
          unset($items[$key]);
      }
   }

   /* Dequeue an item (return it) via specific parameters. Sets a new status after grabbing these records.
   *
   * @param $args Array
            numitems - number of records to pull
            status   - Array - the statusses to pull
            newstatus - To which status said items should be put
            orderby  - how to order the records [not implemented]

      @return Recordset of Items gotten.
   */
   public function dequeue($args = array())
   {
      $defaults = array(
          'numitems' => 1, // pass -1 for all.
          'status' => ShortQ::QSTATUS_WAITING,
          'newstatus' => ShortQ::QSTATUS_DONE,
          'orderby' => 'list_order',
          'order' => 'ASC',
      );

      $args = wp_parse_args($args, $defaults);

      if (is_array($args['status']))
        $args['status'] = implode(',', $args['status']);

      $items = $this->queryItems(array(
        'numitems' => $args['numitems'],
        'status' => $args['status'],
        'orderby' => $args['orderby'],
        'order' => $args['order'],
      ));

      $id_array = array_keys($items);

      // Update status if results yielded.
      if ($args['status'] !== $args['newstatus'] && count($id_array) > 0)
      {
        $now = time();
        $this->updateRecords(array('status' => $args['newstatus'], 'updated' => $now ), array('id' => $id_array));
        foreach($items as $index => $item)
        {
          $item->status = $args['newstatus']; // update status to new situation.
          $item->updated = $now;
          $items[$index] = $item;
        }
      }
      if ($args['newstatus'] == ShortQ::QSTATUS_DELETE)
      {
          $this->removeRecords(array('status' => ShortQ::QSTATUS_DELETE));
      }
      // @todo is Status = QSTATUS_DELETE, remove all records after putting them to this status.

      return array_values($items); // array values resets the id index returns by queryItems
   }



   private function queryItems($args = array())
   {
     $defaults = array(
       'status' => ShortQ::QSTATUS_ALL,
       'orderby' => 'list_order',
       'order' => 'ASC',
       'numitems' => -1,
     );

     $args = wp_parse_args($args, $defaults);

     global $wpdb;
     $prepare = array();

     $sql = 'SELECT * from ' . $this->table . ' where queue_name = %s and plugin_slug = %s ';
     $prepare[] = $this->qName;
     $prepare[] = $this->slug;

     if ($args['status'] <> ShortQ::QSTATUS_ALL)
     {
       $sql .= 'and status = %d ';
       $prepare[] = $args['status'];
     }

     if ($args['orderby'])
     {
       $order = (strtoupper($args['order']) == 'ASC') ? 'ASC ' : 'DESC ';
       $sql .= 'order by ' . $args['orderby'] . ' ' . $order;

      // $prepare[] = $args['orderby'];
     }

     if ($args['numitems'] > 0)
     {
        $sql .= 'limit %d ';
        $prepare[] = $args['numitems'];
     }

     $sql = $wpdb->prepare($sql, $prepare);

     $result = $wpdb->get_results($sql, ARRAY_A);

     $items = array();

     foreach($result as $index => $row)
     {
       $item = new Item();
       $id = $row['id'];
       foreach($row as $name => $value)
       {
         if (property_exists($item, $name))
         {
            $item->$name = $value;
          }
       }
       $items[$id] = $item;
     }

     return $items;
   }

   /** Updates a set of items from queue without pulling or returning those records.
   *
   *  @return int Number of Records Updated
   */
   public function alterqueue($args)
   {

   }

   /** Updates one queued item, for instance in case of failing, or status update
   *
   * @param $item_id int The Uniq Id of the item to update
   * @param $field Array An array of fields in key => pair format to be updated.
   */
   public function itemUpdate(Item $item, $fields)
   {
      $result = $this->updateRecords($fields, array('item_id' => $item->item_id));
      if ($result == 1 )
        return true;
      else
        return false;
   }


   /* Counts Items in Database Queue
   * @param Status Mixed When supplied with ShortQ Status Constant it will count this status, will count all with ShortQ:QSTATUS_ALL.
   * When given 'countbystatus' it will return an array with  ShortQ Status as key and the count as value
     @return Mixed Either count int, or Array.
   */
   public function itemCount($status = ShortQ::QSTATUS_WAITING)
   {
      global $wpdb;
      if (is_numeric($status) && $status != ShortQ::QSTATUS_ALL)
      {
        $sql = 'SELECT count(*) FROM ' . $this->table . ' WHERE queue_name = %s and plugin_slug = %s and status = %d ';
        $count = $wpdb->get_var($wpdb->prepare($sql, $this->qName, $this->slug, $status));
      }
      elseif ($status == ShortQ::QSTATUS_ALL) // full queue, with records from all status.
      {
        $sql = 'SELECT count(*) FROM ' . $this->table . ' WHERE queue_name = %s and plugin_slug = %s ';
        $count = $wpdb->get_var($wpdb->prepare($sql, $this->qName, $this->slug));
      }
      elseif ($status == 'countbystatus')
      {
        $sql = 'SELECT count(id) as count, status FROM ' . $this->table . ' WHERE queue_name = %s and plugin_slug = %s group by status';
        $rows = $wpdb->get_results($wpdb->prepare($sql, $this->qName, $this->slug), ARRAY_A);
        $count = array();

        foreach($rows as $row)
        {
           $count[$row['status']] = $row['count'];
        }


      }

      if (!empty($wpdb->last_error))
      {
        $this->handleError($wpdb->last_error);
        return 0;
      }

      return $count;
   }

   /** Update records
   *
   * @param $Data Array. Data array to change, to WP standards
   * @param $where Array. Data Array on conditions, to WP standards
   * @return int Amount of records updates, or null|false
   */
   private function updateRecords($data, $where)
   {
      global $wpdb;
      $update_sql = 'UPDATE ' . $this->table . ' set updated = %s';
      if (isset($data['updated']))
      {
          $placeholders = array($data['updated']);
          unset($data['updated']);
      }
      else
          $placeholders = array(date('Y-m-d H:i:s'));

      foreach($data as $field => $value)
      {
        $update_sql .= ' ,' . $field . ' = %s ';
        $placeholders[] =  $value;
      }

      $update_sql .= ' WHERE queue_name = %s and plugin_slug = %s ';
      $placeholders[] = $this->qName;
      $placeholders[] = $this->slug;

      foreach ($where as $field => $value)
      {
        if (is_array($value))
        {
          $vals = implode( ', ', array_fill( 0, count( $value ), '%s' ));
          $update_sql .= ' AND ' . $field . ' in (' . $vals . ' ) ';
          $placeholders = array_merge($placeholders, $value);
        }
        else {
          $update_sql .= ' AND ' . $field . ' = %s';
          $placeholders[] = $value;
        }
      }
      $update_sql = $wpdb->prepare($update_sql, $placeholders);

      $result = $wpdb->query($update_sql);
      return $result;
   }

   /** @todo Accept array or ItemIDS to remove
   * @param $args Array . Array of options:
   *  'Status' : remove items with selected ShortQ QSTATUS
   *  'All' : Set to true to remove all from this queue ( sep. argument is safety feature )
   * 'Item_id' : Delete by this item id
   * 'Items' : Array of Item ID's.
   */
   public function removeRecords($args)
   {
     $defaults = array(
        'status' => null,
        'all' => false,
        'item_id' => null,
        'items' => null,
     );

     global $wpdb;
     $args = wp_parse_args($args, $defaults);

     $data = array($this->qName, $this->slug);
     $delete_sql = 'DELETE FROM ' . $this->table . ' where queue_name = %s and plugin_slug = %s';

     if (! is_null($args['status']))
     {
        $data[] = intval($args['status']);
        $delete_sql .= ' and status = %s';
     }
     elseif (! is_null($args['item_id']))
     {
       $data[] = $args['item_id'];
       $delete_sql .= ' and item_id = %s';
     }
     elseif(! is_null($args['items']) && count($args['items']) > 0)
     {
       $items = $args['items'];
       $vals = implode( ', ', array_fill( 0, count( $items ), '%s' ));
       $delete_sql .= ' AND item_id in (' . $vals .  ' ) ';
       $data = array_merge($data, $items);
     }
     elseif ($args['all'] === true)
     {
        // do nothing, query already here for full delete.
     }
     else {
       return false; // prevent accidents if all is not set explicitly.
     }

     $result = $wpdb->query($wpdb->prepare($delete_sql, $data));

     return $result;
   }

   /** Checks if database table properly exists
   * https://wordpress.stackexchange.com/questions/220275/wordpress-unit-testing-cannot-create-tables
   * @return Boolean Yes or no
   */
   private function check()
   {
     global $wpdb;
     $sql = $wpdb->prepare("
              SHOW TABLES LIKE %s
              ", $this->table);

      $result = intval($wpdb->query($sql));

      if ($result == 0)
        return false;
      else {
        return true;
      }
      // if something something, install.
   }

   public function install()
   {
     if ($this->check())
        return true;

     require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

     global $wpdb;

     $charset = $wpdb->get_charset_collate();
      $sql = "CREATE TABLE `" . $this->table . "` (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                queue_name VARCHAR(30) NOT NULL,
                plugin_slug VARCHAR(30) NOT NULL,
                status int(11) NOT NULL DEFAULT 0,
                list_order int(11) NOT NULL,
                item_id INT NOT NULL,
                value longtext NOT NULL,
                tries int(11) NOT NULL DEFAULT 0,
                created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                INDEX queue_name (queue_name),
                INDEX plugin_slug (plugin_slug),
                INDEX status (status),
                INDEX item_id (item_id),
                INDEX list_order (list_order)
                ) $charset; ";

      $result = dbDelta($sql);



      $sql = "SHOW INDEX FROM " . $this->table . " WHERE Key_name = 'uq'";
      $result = $wpdb->get_results($sql);
      if (is_null($result) || count($result) == 0)
      {
         $sql = 'ALTER TABLE '. $this->table . ' ADD CONSTRAINT UNIQUE uq(plugin_slug,queue_name,item_id)';
         $wpdb->query($sql);
      }

      return $this->check();
   }

   public function uninstall()
   {
     global $wpdb;
     require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

     // Check if table exists, if not, return.
     if (! $this->check())
        return false;

     $sql = 'SELECT count(*) as cnt FROM ' . $this->table;
     $records = $wpdb->get_var($sql);


     // Don't remove table on any doubt.
     if (is_null($records) || intval($records) <> 0)
        return false;

     $sql = ' DROP TABLE IF EXISTS ' . $this->table;

     $wpdb->query($sql);
//     dbDelta($sql);

     return $this->check();
   }

   private function checkQueryOK($override_check = false)
   {
      global $wpdb;

      if (!empty($wpdb->last_error))
      {
        $this->handleError($wpdb->last_error, $override_check);
        return false;
      }

      return true;
   }

   private function handleError($error, $override_check = false)
   {
     global $wpdb;

     // check if table is there.
     if (! $override_check)
     {
       if (! $this->check())
        $this->install();
     }

     echo "<PRE> ERROR! ";
      print_r( debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2) );
     echo ($wpdb->last_query);
     var_dump($error);
     echo "</PRE>";
     $this->install();

     // @todo Add error log here
   }


}
