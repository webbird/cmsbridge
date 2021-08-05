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
    public function __construct(string $basedir)
    {
        $cms = $this->identify($basedir);
    }
    
    protected function identify(string $basedir) : string
    {
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
