<?php

namespace MuTMS\CommonMark\Extra;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;

final class ExtraExtension implements ExtensionInterface {
    public function register(EnvironmentBuilderInterface $environment): void {
        // Inline Mathjax rendering using $`1+2`$ GFM syntax.
        $environment
            ->addInlineParser(new MathInlineParser(), 1010)
            ->addRenderer(MathInline::class, new MathInlineRenderer(), 1020);

        // Add Math fenced block renderer.
        $environment
            ->addRenderer(FencedCode::class, new MathBlockRenderer(), 1030);
    }
}
