<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* errors/500.html.twig */
class __TwigTemplate_a8ac195e09e8dbecd6d0040365bd2277 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context): bool|string|Template|TemplateWrapper
    {
        // line 1
        return "layouts/app.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $this->parent = $this->load("layouts/app.html.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield "500 Server Error — ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["app_name"] ?? null), "html", null, true);
        yield from [];
    }

    // line 5
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 6
        yield "<div style=\"text-align: center; padding: 4rem 0;\">
  <h1 style=\"font-size: 5rem; font-weight: 800; color: #94a3b8;\">500</h1>
  <p style=\"font-size: 1.5rem; color: #1e293b; margin: 1rem 0;\">Internal Server Error</p>
  <p style=\"color: #64748b; margin-bottom: 2rem;\">";
        // line 9
        yield (((array_key_exists("message", $context) &&  !(null === $context["message"]))) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["message"], "html", null, true)) : ("Something went wrong. Please try again later."));
        yield "</p>
  ";
        // line 10
        if ((($tmp = ($context["trace"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 11
            yield "    <pre style=\"text-align: left; background: #1e293b; color: #e2e8f0; padding: 1.5rem; border-radius: 0.5rem; overflow: auto; font-size: 0.8rem; margin-top: 2rem;\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["trace"] ?? null), "html", null, true);
            yield "</pre>
  ";
        }
        // line 13
        yield "  <a href=\"/\" style=\"color: #3b82f6; text-decoration: none; font-weight: 500;\">← Back to Home</a>
</div>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "errors/500.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  88 => 13,  82 => 11,  80 => 10,  76 => 9,  71 => 6,  64 => 5,  52 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "errors/500.html.twig", "C:\\Users\\WayneFaustorilla\\Projects\\doctrine-framework\\resources\\views\\errors\\500.html.twig");
    }
}
