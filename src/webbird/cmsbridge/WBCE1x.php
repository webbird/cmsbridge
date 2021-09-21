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
    protected object $conn;
    protected string $cmsname;
    
    public function __construct(\Doctrine\DBAL\Connection $conn) {
        $this->conn = $conn;
    }
    
    /**
     * accessor to db connection
     * 
     * @return \Doctrine\DBAL\Connection
     */
    public function db() : \Doctrine\DBAL\Connection
    {
        return $this->conn;
    }
    
    /**
     * escape string for use with db statements; uses Doctrine's escapeString()
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
        if(empty($this->conn)) {
            throw new \RuntimeException("Missing DB connection, unable to escape string");
        }
        return $this->conn->escapeString($string);
    }
    
    public function formatDate(string $date, ?bool $long=false) : string
    {
        if(is_array($date)) {
            if(isset($date['date'])) {
                $date = $date['date'];
            } else {
                throw new \InvalidArgumentException('Missing date parameter');
            }
        }
        $returndate = gmdate(DATE_FORMAT, $date+TIMEZONE);
        if ($long) {
            $returndate = $returndate . ' ' . gmdate(TIME_FORMAT, $date+TIMEZONE);
        }
        return $returndate;
    }
    
    public function getDelimiter() : string
    {
        return '&';
    }
    
    /**
     * 
     * @param int $pageID
     * @return string
     * @throws InvalidArgumentException
     */
    public function getEditURL(int $pageID) : string
    {
        if(is_array($pageID)) {
            if(isset($pageID['pageID'])) {
                $pageID = $pageID['pageID'];
            } else {
                throw new InvalidArgumentException('Missing pageID parameter');
            }
        }
        return ADMIN_URL.'/pages/modify.php?page_id='.$pageID;
    }
    
    /**
     * get full name (incl. version)
     * 
     * @return string
     */
    public function getFullCMSName() : string
    {
        if(empty($this->cmsname)) {
            $resultSet = $this->conn->executeQuery(sprintf(
                'SELECT `value` FROM `%ssettings` WHERE `name`="wbce_version"', $this->prefix()
            ));
            $result = $resultSet->fetchAssociative();
            $this->cmsname = 'WBCE Version '.$result['value'];
        }
        return $this->cmsname;
    }
    
    /**
     * returns the page_id for the given $sectionID
     * 
     * @global object $wb
     * @param mixed $sectionID
     * @return int
     * @throws InvalidArgumentException
     */
    public function getPageForSection(int $sectionID) : int
    {
        global $wb;
        if(is_array($sectionID)) {
            if(isset($sectionID['sectionID'])) {
                $sectionID = $sectionID['sectionID'];
            } else {
                throw new InvalidArgumentException('Missing sectionID parameter');
            }
        }
        $section = $wb->get_section_details($sectionID);
        return (isset($section['page_id']) ? intval($section['page_id']) : 0);
    }
    
    /**
     * get current backend theme
     * 
     * @return string
     */
    public function getThemeName() : string
    {
        $resultSet = $this->conn->executeQuery(sprintf(
            'SELECT `value` FROM `%ssettings` WHERE `name`="default_theme"', $this->prefix()
        ));
        $result = $resultSet->fetchAssociative();
        return (string)$result['value'];
    }
    
    /**
     * returns WB_URL
     * 
     * @return string
     */
    public function getURL() : string
    {
        return WB_URL;
    }
    
    public function getWYSIWYGEditor(
        string $id, 
        string $content, 
        string $width, 
        string $height
    ) : string
    {
        if (!function_exists('show_wysiwyg_editor')) {
            if (file_exists(WB_PATH.'/modules/ckeditor/include.php')) {
                include_once WB_PATH.'/modules/ckeditor/include.php';
            }
            if (!function_exists('show_wysiwyg_editor')) {
                return "<textarea name=\"$id\" id=\"$id\" style=\"width:$width;height:$height\">$content</textarea><br />\n".
                     "<span style=\"color:#c00;font-size:smaller\">".
                     self::$i18n->t('Please note: There is no WYSIWYG Editor installed').
                     "</span>\n";
            }
        }
        if (function_exists('show_wysiwyg_editor')) {
            ob_start();
                show_wysiwyg_editor($id, $id, $content, $width, $height);
                $editor = ob_get_contents();
            ob_end_clean();
            return $editor;
        }
    }

    /**
     * returns the table prefix
     * 
     * @return string
     */
    public function prefix() : string
    {
        return defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
    }
}
