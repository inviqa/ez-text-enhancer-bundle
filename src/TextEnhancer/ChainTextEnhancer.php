<?php

namespace Inviqa\EzTextEnhancerBundle\TextEnhancer;

use Inviqa\EzTextEnhancerBundle\TextEnhancer;

final class ChainTextEnhancer implements TextEnhancer
{
    /**
     * @var TextEnhancer[]
     */
    private $textEnhancers;

    public function __construct()
    {
        $this->textEnhancers = [];
    }

    /**
     * @param TextEnhancer $textEnhancer
     */
    public function addTextEnhancer(TextEnhancer $textEnhancer)
    {
        $this->textEnhancers[] = $textEnhancer;
    }

    /**
     * @inheritDoc
     */
    public function enhance($text)
    {
        foreach ($this->textEnhancers as $textEnhancer) {
            $text = $textEnhancer->enhance($text);
        }

        return $text;
    }
}
