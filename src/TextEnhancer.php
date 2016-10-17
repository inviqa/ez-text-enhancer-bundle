<?php

namespace Inviqa\EzTextEnhancerBundle;

interface TextEnhancer
{
    /**
     * @param string $text
     *
     * @return string
     */
    public function enhance($text);
}
