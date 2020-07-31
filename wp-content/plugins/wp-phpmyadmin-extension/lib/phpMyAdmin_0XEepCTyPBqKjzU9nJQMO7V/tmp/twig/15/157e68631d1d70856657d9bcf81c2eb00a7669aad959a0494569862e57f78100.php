<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* server/status/base.twig */
class __TwigTemplate_aa8dbf60cca01709f28e4a09d0387b05dfba69289b84bd837fa11bf7839c3702 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<div>
  <ul id=\"topmenu2\">
    <li>
      <a href=\"server_status.php";
        // line 4
        echo PhpMyAdmin\Url::getCommon();
        echo "\"";
        echo (((($context["active"] ?? null) == "status")) ? (" class=\"tabactive\"") : (""));
        echo ">
        ";
        // line 5
        echo _gettext("Server");
        // line 6
        echo "      </a>
    </li>
    <li>
      <a href=\"server_status_processes.php";
        // line 9
        echo PhpMyAdmin\Url::getCommon();
        echo "\"";
        echo (((($context["active"] ?? null) == "processes")) ? (" class=\"tabactive\"") : (""));
        echo ">
        ";
        // line 10
        echo _gettext("Processes");
        // line 11
        echo "      </a>
    </li>
    <li>
      <a href=\"server_status_queries.php";
        // line 14
        echo PhpMyAdmin\Url::getCommon();
        echo "\"";
        echo (((($context["active"] ?? null) == "queries")) ? (" class=\"tabactive\"") : (""));
        echo ">
        ";
        // line 15
        echo _gettext("Query statistics");
        // line 16
        echo "      </a>
    </li>
    <li>
      <a href=\"server_status_variables.php";
        // line 19
        echo PhpMyAdmin\Url::getCommon();
        echo "\"";
        echo (((($context["active"] ?? null) == "variables")) ? (" class=\"tabactive\"") : (""));
        echo ">
        ";
        // line 20
        echo _gettext("All status variables");
        // line 21
        echo "      </a>
    </li>
    <li>
      <a href=\"server_status_monitor.php";
        // line 24
        echo PhpMyAdmin\Url::getCommon();
        echo "\"";
        echo (((($context["active"] ?? null) == "monitor")) ? (" class=\"tabactive\"") : (""));
        echo ">
        ";
        // line 25
        echo _gettext("Monitor");
        // line 26
        echo "      </a>
    </li>
    <li>
      <a href=\"server_status_advisor.php";
        // line 29
        echo PhpMyAdmin\Url::getCommon();
        echo "\"";
        echo (((($context["active"] ?? null) == "advisor")) ? (" class=\"tabactive\"") : (""));
        echo ">
        ";
        // line 30
        echo _gettext("Advisor");
        // line 31
        echo "      </a>
    </li>
  </ul>
  <div class=\"clearfloat\"></div>

  <div>
    ";
        // line 37
        $this->displayBlock('content', $context, $blocks);
        // line 38
        echo "  </div>
</div>
";
    }

    // line 37
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    public function getTemplateName()
    {
        return "server/status/base.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  132 => 37,  126 => 38,  124 => 37,  116 => 31,  114 => 30,  108 => 29,  103 => 26,  101 => 25,  95 => 24,  90 => 21,  88 => 20,  82 => 19,  77 => 16,  75 => 15,  69 => 14,  64 => 11,  62 => 10,  56 => 9,  51 => 6,  49 => 5,  43 => 4,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/status/base.twig", "/scratch/html/wp-content/plugins/wp-phpmyadmin-extension/lib/phpMyAdmin_0XEepCTyPBqKjzU9nJQMO7V/templates/server/status/base.twig");
    }
}
