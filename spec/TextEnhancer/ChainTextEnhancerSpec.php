<?php

namespace spec\Inviqa\EzTextEnhancerBundle\TextEnhancer;

use Inviqa\EzTextEnhancerBundle\TextEnhancer;
use PhpSpec\ObjectBehavior;

class ChainTextEnhancerSpec extends ObjectBehavior
{
    function it_is_a_TextEnhancer()
    {
        $this->shouldHaveType(TextEnhancer::class);
    }

    function it_enhances_a_body_by_applying_all_the_enhancers(TextEnhancer $textEnhancer1, TextEnhancer $textEnhancer2)
    {
        $textEnhancer1->enhance('my text')->willReturn('my enhanced text');
        $textEnhancer2->enhance('my enhanced text')->willReturn('my really enhanced text');

        $this->addTextEnhancer($textEnhancer1);
        $this->addTextEnhancer($textEnhancer2);
        $this->enhance('my text')->shouldReturn('my really enhanced text');
    }
}
