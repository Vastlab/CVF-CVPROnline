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

/* table/search/replace_preview.twig */
class __TwigTemplate_4d43b3c5e3836f0119ca3d9dd99973c3dd4c5cb6455bf0a4132c364701f8a870 extends \Twig\Template
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
        echo "<form method=\"post\"
      action=\"tbl_find_replace.php\"
      name=\"previewForm\"
      id=\"previewForm\">
    ";
        // line 5
        echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null), ($context["table"] ?? null));
        echo "
    <input type=\"hidden\" name=\"replace\" value=\"true\">
    <input type=\"hidden\" name=\"columnIndex\" value=\"";
        // line 7
        echo twig_escape_filter($this->env, ($context["column_index"] ?? null), "html", null, true);
        echo "\">
    <input type=\"hidden\" name=\"findString\" value=\"";
        // line 8
        echo twig_escape_filter($this->env, ($context["find"] ?? null), "html", null, true);
        echo "\">
    <input type=\"hidden\" name=\"replaceWith\" value=\"";
        // line 9
        echo twig_escape_filter($this->env, ($context["replace_with"] ?? null), "html", null, true);
        echo "\">
    <input type=\"hidden\" name=\"useRegex\" value=\"";
        // line 10
        echo twig_escape_filter($this->env, ($context["use_regex"] ?? null), "html", null, true);
        echo "\">

    <fieldset id=\"fieldset_find_replace_preview\">
        <legend>";
        // line 13
        echo _gettext("Find and replace - preview");
        echo "</legend>
        <table id=\"previewTable\">
            <thead>
            <tr>
                <th>";
        // line 17
        echo _gettext("Count");
        echo "</th>
                <th>";
        // line 18
        echo _gettext("Original string");
        echo "</th>
                <th>";
        // line 19
        echo _gettext("Replaced string");
        echo "</th>
            </tr>
            </thead>
            <tbody>
            ";
        // line 23
        if (twig_test_iterable(($context["result"] ?? null))) {
            // line 24
            echo "                ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["result"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["row"]) {
                // line 25
                echo "                    <tr>
                        <td class=\"right\">";
                // line 26
                echo twig_escape_filter($this->env, (($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 = $context["row"]) && is_array($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4) || $__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 instanceof ArrayAccess ? ($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4[2] ?? null) : null), "html", null, true);
                echo "</td>";
                // line 27
                echo "                        <td>";
                echo twig_escape_filter($this->env, (($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 = $context["row"]) && is_array($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144) || $__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 instanceof ArrayAccess ? ($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144[0] ?? null) : null), "html", null, true);
                echo "</td>";
                // line 28
                echo "                        <td>";
                echo twig_escape_filter($this->env, (($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b = $context["row"]) && is_array($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b) || $__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b instanceof ArrayAccess ? ($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b[1] ?? null) : null), "html", null, true);
                echo "</td>";
                // line 29
                echo "                    </tr>
                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['row'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 31
            echo "            ";
        }
        // line 32
        echo "            </tbody>
        </table>
    </fieldset>

    <fieldset class=\"tblFooters\">
        <input class=\"btn btn-secondary\" type=\"submit\" name=\"replace\" value=\"";
        // line 37
        echo _gettext("Replace");
        echo "\">
    </fieldset>
</form>
";
    }

    public function getTemplateName()
    {
        return "table/search/replace_preview.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  126 => 37,  119 => 32,  116 => 31,  109 => 29,  105 => 28,  101 => 27,  98 => 26,  95 => 25,  90 => 24,  88 => 23,  81 => 19,  77 => 18,  73 => 17,  66 => 13,  60 => 10,  56 => 9,  52 => 8,  48 => 7,  43 => 5,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "table/search/replace_preview.twig", "/var/www/html/wp-content/plugins/wp-phpmyadmin-extension/lib/phpMyAdmin_0XEepCTyPBqKjzU9nJQMO7V/templates/table/search/replace_preview.twig");
    }
}
