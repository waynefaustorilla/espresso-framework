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

/* layouts/app.html.twig */
class __TwigTemplate_27e170ea55d03ff598828590b924e714 extends Template
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

        $this->parent = false;

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'styles' => [$this, 'block_styles'],
            'content' => [$this, 'block_content'],
            'scripts' => [$this, 'block_scripts'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<!DOCTYPE html>
<html lang=\"en\">
<head>
  <meta charset=\"UTF-8\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
  <title>";
        // line 6
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        yield "</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: system-ui, -apple-system, sans-serif; background: #f9fafb; color: #111827; }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
    nav { background: #1e293b; padding: 1rem 0; }
    nav .container { display: flex; justify-content: space-between; align-items: center; }
    nav a { color: #e2e8f0; text-decoration: none; font-weight: 500; }
    nav a:hover { color: #fff; }
    main { padding: 2rem 0; }
    footer { background: #1e293b; color: #94a3b8; text-align: center; padding: 1.5rem 0; margin-top: 4rem; }
  </style>
  ";
        // line 18
        yield from $this->unwrap()->yieldBlock('styles', $context, $blocks);
        // line 19
        yield "</head>
<body>
  <nav>
    <div class=\"container\">
      <a href=\"/\">";
        // line 23
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["app_name"] ?? null), "html", null, true);
        yield "</a>
      <div>
        ";
        // line 25
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["auth"] ?? null), "check", [], "method", false, false, false, 25)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 26
            yield "          <a href=\"/logout\">Logout</a>
        ";
        } else {
            // line 28
            yield "          <a href=\"/login\">Login</a>
        ";
        }
        // line 30
        yield "      </div>
    </div>
  </nav>

  <main>
    <div class=\"container\">
      ";
        // line 36
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        // line 37
        yield "    </div>
  </main>

  <footer>
    <div class=\"container\">
      &copy; ";
        // line 42
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate("now", "Y"), "html", null, true);
        yield " ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["app_name"] ?? null), "html", null, true);
        yield "
    </div>
  </footer>

  ";
        // line 46
        yield from $this->unwrap()->yieldBlock('scripts', $context, $blocks);
        // line 47
        yield "</body>
</html>
";
        yield from [];
    }

    // line 6
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["app_name"] ?? null), "html", null, true);
        yield from [];
    }

    // line 18
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_styles(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield from [];
    }

    // line 36
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield from [];
    }

    // line 46
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_scripts(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "layouts/app.html.twig";
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
        return array (  157 => 46,  147 => 36,  137 => 18,  126 => 6,  119 => 47,  117 => 46,  108 => 42,  101 => 37,  99 => 36,  91 => 30,  87 => 28,  83 => 26,  81 => 25,  76 => 23,  70 => 19,  68 => 18,  53 => 6,  46 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "layouts/app.html.twig", "C:\\Users\\WayneFaustorilla\\Projects\\doctrine-framework\\resources\\views\\layouts\\app.html.twig");
    }
}
