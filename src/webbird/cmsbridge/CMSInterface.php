<?php

declare(strict_types=1);

namespace webbird\cmsbridge;

interface CMSInterface
{
    public function db() : \Doctrine\DBAL\Connection;
    public function formatDate(string $date, ?bool $long=false) : string;
    public function getDelimiter() : string;
    public function getEditURL(mixed $pageID) : string;
    public function getFullCMSName() : string;
    public function getPageForSection(int $sectionID) : int;
    public function getThemeName() : string;
    public function getURL() : string;
    public function prefix() : string;
    public function escapeString(string $string) : string;
}