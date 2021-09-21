<?php

declare(strict_types=1);

namespace webbird\cmsbridge;

/**
 * Description of Bridge
 *
 * @author bmartino
 */
class Bridge 
{
    protected static object $adapter;
    
    public static function getAdapter()
    {
        if(empty(self::$adapter)) {
            throw new \RuntimeException('Static function getAdapter() called before adapter was loaded');
        }
        return self::$adapter;
    }
    
    /**
     * 
     * @param string $basedir
     * @return self
     * @throws \RuntimeException
     */
    public function __construct(\Doctrine\DBAL\Connection $conn)
    {
        $cms = $this->identify();
        if($cms != 'unknown') {
            define('CMS_NAME', $cms);
            $class = '\webbird\cmsbridge\\'.$cms;
            try {
                self::$adapter = new $class($conn);
            } catch ( \Exception $e ) {
                throw new \RuntimeException(sprintf('No adapter found for CMS %s',$cms));
            }
        }
    }
    
    public function db() : \Doctrine\DBAL\Connection
    {
        return self::$adapter->db();
    }
    
    public function formatDate(string $date, ?bool $long=false)
    {
        return self::$adapter->formatDate($date, $long);
    }
    
    public function getDelimiter() : string
    {
        return self::$adapter->getDelimiter();
    }
    
    public function getEditURL(int $pageID) : string
    {
        return self::$adapter->getEditURL($pageID);
    }
    
    public function getFullCMSName() : string
    {
        return self::$adapter->getFullCMSName();
    }
    
    public function getPageForSection(int $sectionID) : int
    {
        return self::$adapter->getPageForSection($sectionID);
    }
    
    public function getThemeName() : string
    {
        return self::$adapter->getThemeName();
    }
    
    public function getURL() : string
    {
        return self::$adapter->getURL();
    }
    
    public function getWYSIWYGEditor(string $id, string $content, string $width, string $height ) : string
    {
        return self::$adapter->getWYSIWYGEditor($id, $content, $width, $height);
    }
    
    public function prefix() : string
    {
        return self::$adapter->prefix();
    }
    
    /**
     * 
     * @return string
     * @throws RuntimeException
     */
    protected function identify() : string
    {
        $basedir = __DIR__;
        $n = 0; // avoid endless loop
        // go up until config.php is found
        while(!file_exists($basedir.'/config.php')) {
            $basedir = dirname($basedir);
            if($n>10) { 
                throw new RuntimeException('Basedir not found (this is where the config.php resides)');
            }
        }
        // WBCE
        if (file_exists($basedir.'/framework/Insert.php')) {
            return "WBCE1x";
        }
        // BC2
        if (file_exists($basedir.'/CAT/Hook.php')) {
            return "BlackCat2x";
        }
        if (file_exists($basedir.'/framework/CAT/Object.php')) {
            return "BlackCat1x";
        }
        return 'unknown';
    }   // end function identify()
}
