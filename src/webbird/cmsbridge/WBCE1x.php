<?php

declare(strict_types=1);

namespace webbird\cmsbridge;

/**
 * Description of WBCE1x
 *
 * @author bmartino
 */
class WBCE1x 
{
    protected static $db;
    
    /**
     * 
     * @param type $database
     * @return \Doctrine\DBAL\Connection
     */
    public function getDBHandle($database) : \Doctrine\DBAL\Connection
    {
        if(empty(self::$db)) {
            if (is_object($database) && is_object($database->db_handle) && $database->db_handle instanceof \Doctrine\DBAL\Connection) {
                self::$db = $database;
            }
            self::$prefix = \defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
        }
        return self::$db;
    }
    
    /**
     * 
     * @param string $string
     * @return string
     * @throws \RuntimeException
     */
    public function escapeString(string $string) : string
    {
        if (empty($string)) {
            return '';
        }
        if(empty(self::$db)) {
            throw new \RuntimeException("Method escapeString() called before getDBHandle()");
        }
        return self::$db->escapeString($string);
    }
}
