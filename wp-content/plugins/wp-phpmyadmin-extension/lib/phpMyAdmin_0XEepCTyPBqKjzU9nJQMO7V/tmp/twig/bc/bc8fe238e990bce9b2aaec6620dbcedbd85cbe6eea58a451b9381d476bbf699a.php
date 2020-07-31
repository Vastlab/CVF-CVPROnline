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

/* server/status/status/index.twig */
class __TwigTemplate_ec8ff21388dcb542e4735ad2316bf8909dfe93bcff80fdf8287b518e3d3327b3 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "server/status/base.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 2
        $context["active"] = "status";
        // line 1
        $this->parent = $this->loadTemplate("server/status/base.twig", "server/status/status/index.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "
";
        // line 5
        if (($context["is_data_loaded"] ?? null)) {
            // line 6
            echo "  <h3>";
            echo twig_escape_filter($this->env, sprintf(_gettext("Network traffic since startup: %s"), ($context["network_traffic"] ?? null)), "html", null, true);
            echo "</h3>
  <p>";
            // line 7
            echo twig_escape_filter($this->env, sprintf(_gettext("This MySQL server has been running for %1\$s. It started up on %2\$s."), ($context["uptime"] ?? null), ($context["start_time"] ?? null)), "html", null, true);
            echo "</p>

  <table id=\"serverstatustraffic\" class=\"width100 data noclick\">
    <thead>
      <tr>
        <th>
          ";
            // line 13
            echo _gettext("Traffic");
            // line 14
            echo "          ";
            echo PhpMyAdmin\Util::showHint(_gettext("On a busy server, the byte counters may overrun, so those statistics as reported by the MySQL server may be incorrect."));
            echo "
        </th>
        <th>#</th>
        <th>&oslash; ";
            // line 17
            echo _gettext("per hour");
            echo "</th>
      </tr>
    </thead>

    <tbody>
      ";
            // line 22
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["traffic"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["each_traffic"]) {
                // line 23
                echo "        <tr>
          <th class=\"name\">";
                // line 24
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["each_traffic"], "name", [], "any", false, false, false, 24), "html", null, true);
                echo "</th>
          <td class=\"value\">";
                // line 25
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["each_traffic"], "number", [], "any", false, false, false, 25), "html", null, true);
                echo "</td>
          <td class=\"value\">";
                // line 26
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["each_traffic"], "per_hour", [], "any", false, false, false, 26), "html", null, true);
                echo "</td>
        </tr>
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['each_traffic'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 29
            echo "    </tbody>
  </table>

  <table id=\"serverstatusconnections\" class=\"width100 data noclick\">
    <thead>
      <tr>
        <th>";
            // line 35
            echo _gettext("Connections");
            echo "</th>
        <th>#</th>
        <th>&oslash; ";
            // line 37
            echo _gettext("per hour");
            echo "</th>
        <th>%</th>
      </tr>
    </thead>

    <tbody>
      ";
            // line 43
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["connections"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["connection"]) {
                // line 44
                echo "        <tr>
          <th class=\"name\">";
                // line 45
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["connection"], "name", [], "any", false, false, false, 45), "html", null, true);
                echo "</th>
          <td class=\"value\">";
                // line 46
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["connection"], "number", [], "any", false, false, false, 46), "html", null, true);
                echo "</td>
          <td class=\"value\">";
                // line 47
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["connection"], "per_hour", [], "any", false, false, false, 47), "html", null, true);
                echo "</td>
          <td class=\"value\">";
                // line 48
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["connection"], "percentage", [], "any", false, false, false, 48), "html", null, true);
                echo "</td>
        </tr>
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['connection'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 51
            echo "    </tbody>
  </table>

  ";
            // line 54
            if ((($context["is_master"] ?? null) || ($context["is_slave"] ?? null))) {
                // line 55
                echo "    <p class=\"notice clearfloat\">
      ";
                // line 56
                if ((($context["is_master"] ?? null) && ($context["is_slave"] ?? null))) {
                    // line 57
                    echo "        ";
                    echo _gettext("This MySQL server works as <b>master</b> and <b>slave</b> in <b>replication</b> process.");
                    // line 58
                    echo "      ";
                } elseif (($context["is_master"] ?? null)) {
                    // line 59
                    echo "        ";
                    echo _gettext("This MySQL server works as <b>master</b> in <b>replication</b> process.");
                    // line 60
                    echo "      ";
                } elseif (($context["is_slave"] ?? null)) {
                    // line 61
                    echo "        ";
                    echo _gettext("This MySQL server works as <b>slave</b> in <b>replication</b> process.");
                    // line 62
                    echo "      ";
                }
                // line 63
                echo "    </p>

    <hr class=\"clearfloat\">

    <h3>";
                // line 67
                echo _gettext("Replication status");
                echo "</h3>

    ";
                // line 69
                echo ($context["replication"] ?? null);
                echo "
  ";
            }
            // line 71
            echo "
";
        } else {
            // line 73
            echo "  ";
            echo call_user_func_array($this->env->getFilter('error')->getCallable(), [_gettext("Not enough privilege to view server status.")]);
            echo "
";
        }
        // line 75
        echo "
";
    }

    public function getTemplateName()
    {
        return "server/status/status/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  219 => 75,  213 => 73,  209 => 71,  204 => 69,  199 => 67,  193 => 63,  190 => 62,  187 => 61,  184 => 60,  181 => 59,  178 => 58,  175 => 57,  173 => 56,  170 => 55,  168 => 54,  163 => 51,  154 => 48,  150 => 47,  146 => 46,  142 => 45,  139 => 44,  135 => 43,  126 => 37,  121 => 35,  113 => 29,  104 => 26,  100 => 25,  96 => 24,  93 => 23,  89 => 22,  81 => 17,  74 => 14,  72 => 13,  63 => 7,  58 => 6,  56 => 5,  53 => 4,  49 => 3,  44 => 1,  42 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/status/status/index.twig", "/scratch/html/wp-content/plugins/wp-phpmyadmin-extension/lib/phpMyAdmin_0XEepCTyPBqKjzU9nJQMO7V/templates/server/status/status/index.twig");
    }
}
