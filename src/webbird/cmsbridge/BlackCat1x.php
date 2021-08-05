<?php

declare(strict_types=1);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace webbird\cmsbridge;

/**
 * Description of BlackCat1x
 *
 * @author bmartino
 */
class BlackCat1x 
{
    protected static $db;
    protected static $prefix;

    /**
     * 
     * @param type $database
     * @return \Doctrine\DBAL\Connection
     */
    public function getDBHandle($database) : \Doctrine\DBAL\Connection
    {
        if(empty(self::$db)) {
            self::$db = $database;
            self::$prefix =  \defined('CAT_TABLE_PREFIX') ? CAT_TABLE_PREFIX
                          : (\defined('TABLE_PREFIX')     ? TABLE_PREFIX
                          : '');
        }
        return self::$db;
    }
    
    public function escapeString(string $string) : string
    {
        if (empty($string)) {
            return '';
        }
        if(empty(self::$db)) {
            throw new \RuntimeException("Method escapeString() called before getDBHandle()");
        }
        $quoted = self::db()->conn()->quote($string);
        $quoted = substr_replace($quoted, '', 0, 1);
        $quoted = substr_replace($quoted, '', -1, 1);
        return $quoted;
    }
}
