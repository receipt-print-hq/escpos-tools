<?php

namespace ReceiptPrintHq\EscposTools\Parser\Context;

use ReceiptPrintHq\EscposTools\Parser\Context\ParserContext;
use Mike42\Escpos\CapabilityProfile;

class ParserContextImpl implements ParserContext
{
    protected $profile;

    public function getProfile()
    {
        return $this -> profile;
    }
    
    public function setProfile(CapabilityProfile $profile)
    {
        $this -> profile = $profile;
    }
    
    public static function byProfileName($profileName)
    {
        $ctx = new ParserContextImpl();
        $profile = CapabilityProfile::load($profileName);
        $ctx -> setProfile($profile);
        return $ctx;
    }
}
