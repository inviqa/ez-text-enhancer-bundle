<?php

namespace Inviqa\EzTextEnhancerBundle\Twig;

use eZ\Publish\Core\FieldType\RichText\Converter;
use Inviqa\EzTextEnhancerBundle\TextEnhancer;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Unfortunately, Twig extensions (like that one) are attached to the twig service. The drawback of this situation
 * is that you cannot inject the twig service to it or any of its dependencies as you would create a circular
 * reference exception. This situation can be (potentially) reverted once the `initRuntime` method in `Twig_Extension`
 * gets removed.
 *
 * For now, the only solution is to either inject the container into this extension or its dependencies and, since we
 * expect many dependencies, we prefer to keep the "dirty" solution in a single place. THIS IS WHY THIS SERVICE REQUIRES
 * THE CONTAINER.
 */
final class TwigTextEnhancerExtension extends \Twig_Extension
{
    const TEXT_ENHANCER_SERVICE_ID = 'inviqa.text_enhancer.chain_text_enhancer';

    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $textEnhancerServiceId;

    /**
     * @param Converter          $converter
     * @param ContainerInterface $container
     * @param string             $textEnhancerServiceId
     */
    public function __construct(
        Converter $converter,
        ContainerInterface $container,
        $textEnhancerServiceId = self::TEXT_ENHANCER_SERVICE_ID
    ) {
        $this->converter = $converter;
        $this->container = $container;
        $this->textEnhancerServiceId = $textEnhancerServiceId;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'TextEnhancerExtension';
    }

    /**
     * @inheritDoc
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('enhance_text', [$this, 'enhanceText'], ['is_safe' => ['all']]),
        ];
    }

    /**
     * @param string|\DOMDocument $text
     *
     * @return string
     */
    public function enhanceText($text)
    {
        return $this->getTextEnhanceService()->enhance($this->sanitiseText($text));
    }

    /**
     * @param string|\DOMDocument $text
     *
     * @return string
     */
    private function sanitiseText($text)
    {
        if ($text instanceof \DOMDocument) {
            $text = $this->converter->convert($text)->saveHTML();
        }

        return $text;
    }

    /**
     * @return TextEnhancer
     */
    private function getTextEnhanceService()
    {
        return $this->container->get($this->textEnhancerServiceId);
    }
}
