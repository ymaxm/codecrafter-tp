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

/* sql/sql_query_results.twig */
class __TwigTemplate_dab7e27b4319c887a36d55da0821b4c827281c3bfabf37ddf6d35f838da5fd02 extends Template
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
        yield "<div class=\"sqlqueryresults ajax\">
    ";
        // line 2
        yield ($context["previous_update_query"] ?? null);
        yield "
    ";
        // line 3
        yield ($context["profiling_chart"] ?? null);
        yield "
    ";
        // line 4
        yield ($context["missing_unique_column_message"] ?? null);
        yield "
    ";
        // line 5
        yield ($context["bookmark_created_message"] ?? null);
        yield "
    ";
        // line 6
        yield ($context["table"] ?? null);
        yield "
    ";
        // line 7
        yield ($context["bookmark_support"] ?? null);
        yield "
</div>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "sql/sql_query_results.twig";
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
        return array (  61 => 7,  57 => 6,  53 => 5,  49 => 4,  45 => 3,  41 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "sql/sql_query_results.twig", "C:\\phpstudy_pro\\WWW\\tp\\public\\phpMyAdmin5.2.2\\templates\\sql\\sql_query_results.twig");
    }
}
