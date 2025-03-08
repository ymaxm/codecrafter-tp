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

/* select_all.twig */
class __TwigTemplate_e2def18f33e1dacc4bfd79a1411d3fd9f05e6a87da6e18ba3d706cf53edf3c74 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        yield "<img class=\"selectallarrow\" src=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['PhpMyAdmin\Twig\AssetExtension']->getImagePath((("arrow_" . ($context["text_dir"] ?? null)) . ".png")), "html", null, true);
        yield "\"
    width=\"38\" height=\"22\" alt=\"";
yield _gettext("With selected:");
        // line 2
        yield "\">
<input type=\"checkbox\" id=\"";
        // line 3
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["form_name"] ?? null), "html", null, true);
        yield "_checkall\" class=\"checkall_box\"
    title=\"";
yield _gettext("Check all");
        // line 4
        yield "\">
<label for=\"";
        // line 5
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["form_name"] ?? null), "html", null, true);
        yield "_checkall\">";
yield _gettext("Check all");
        yield "</label>
<em class=\"with-selected\">";
yield _gettext("With selected:");
        // line 6
        yield "</em>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "select_all.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  62 => 6,  55 => 5,  52 => 4,  47 => 3,  44 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "select_all.twig", "C:\\phpstudy_pro\\WWW\\tp\\public\\phpMyAdmin5.2.2\\templates\\select_all.twig");
    }
}
