<?php

namespace spec\Inviqa\EzTextEnhancerBundle\Twig;

use eZ\Publish\Core\FieldType\RichText\Converter;
use Inviqa\EzTextEnhancerBundle\TextEnhancer;
use Inviqa\EzTextEnhancerBundle\Twig\TwigTextEnhancerExtension;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TwigTextEnhancerExtensionSpec extends ObjectBehavior
{
    function let(Converter $converter, TextEnhancer $textEnhancer, ContainerInterface $container)
    {
        $container->get(TwigTextEnhancerExtension::TEXT_ENHANCER_SERVICE_ID)->willReturn($textEnhancer);
        $textEnhancer->enhance("<p>my text</p>\n")->willReturn('<p>my <strong>enhanced</strong> text</p>');

        $this->beConstructedWith($converter, $container);
    }

    function it_knows_its_name()
    {
        $this->getName()->shouldReturn('TextEnhancerExtension');
    }

    function it_knows_the_filters_it_implements()
    {
        $this->getFilters()->shouldReturnTheFilters(['enhance_text']);
    }
    
    function it_enhances_the_text_if_string_is_received(Converter $converter)
    {
        $converter->convert(Argument::type(\DOMDocument::class))->shouldNotBeCalled();

        $this->enhanceText("<p>my text</p>\n")->shouldReturn('<p>my <strong>enhanced</strong> text</p>');
    }

    function it_converts_the_text_and_then_it_enhances_it_if_DOMDocument_is_received(Converter $converter)
    {
        $inputDocument = new \DOMDocument();
        $inputDocument->loadXML('<p>void</p>');

        $outputDocument = new \DOMDocument();
        $outputDocument->loadXML('<p>my text</p>');

        $converter->convert($inputDocument)->willReturn($outputDocument);

        $this->enhanceText($inputDocument)->shouldReturn('<p>my <strong>enhanced</strong> text</p>');
    }

    public function getMatchers()
    {
        return [
            'returnTheFilters' => function ($filters, $expected)
            {
                if (!is_array($filters)) {
                    return false;
                }

                if (count($filters) !== count($expected)) {
                    return false;
                }

                for ($i = 0; $i < count($filters); $i++) {
                    if (!$filters[$i] instanceof \Twig_SimpleFilter) {
                        return false;
                    }

                    if ($filters[$i]->getName() !== $expected[$i]) {
                        return false;
                    }
                }

                return true;
            }
        ];
    }
}
