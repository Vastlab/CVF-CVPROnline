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

/* table/search/zoom_result_form.twig */
class __TwigTemplate_a82d58a2a9faf0cf4d87f8acdbddd923cc33b7da2d5e883494597b0bb751a7b2 extends \Twig\Template
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
        echo "<form method=\"post\" action=\"tbl_zoom_select.php\" name=\"displayResultForm\" id=\"zoom_display_form\" class=\"ajax\">
    ";
        // line 2
        echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null), ($context["table"] ?? null));
        echo "
    <input type=\"hidden\" name=\"goto\" value=\"";
        // line 3
        echo twig_escape_filter($this->env, ($context["goto"] ?? null), "html", null, true);
        echo "\">
    <input type=\"hidden\" name=\"back\" value=\"tbl_zoom_select.php\">

    <fieldset id=\"displaySection\">
        <legend>";
        // line 7
        echo _gettext("Browse/Edit the points");
        echo "</legend>

        ";
        // line 10
        echo "        <center>
            ";
        // line 11
        if ((($context["zoom_submit"] ?? null) &&  !twig_test_empty(($context["data"] ?? null)))) {
            // line 12
            echo "                <div id=\"resizer\">
                    <center>
                        <a id=\"help_dialog\" href=\"#\">
                            ";
            // line 15
            echo _gettext("How to use");
            // line 16
            echo "                        </a>
                    </center>
                    <div id=\"querydata\" class=\"hide\">
                        ";
            // line 19
            echo twig_escape_filter($this->env, ($context["data_json"] ?? null), "html", null, true);
            echo "
                    </div>
                    <div id=\"querychart\"></div>
                    <button class=\"button-reset\">
                        ";
            // line 23
            echo _gettext("Reset zoom");
            // line 24
            echo "                    </button>
                </div>
            ";
        }
        // line 27
        echo "        </center>

        ";
        // line 30
        echo "        <div id=\"dataDisplay\" class=\"hide\">
            <table>
                <thead>
                <tr>
                    <th>";
        // line 34
        echo _gettext("Column");
        echo "</th>
                    <th>";
        // line 35
        echo _gettext("Null");
        echo "</th>
                    <th>";
        // line 36
        echo _gettext("Value");
        echo "</th>
                </tr>
                </thead>
                <tbody>
                ";
        // line 40
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(range(0, (twig_length_filter($this->env, ($context["column_names"] ?? null)) - 1)));
        foreach ($context['_seq'] as $context["_key"] => $context["column_index"]) {
            // line 41
            echo "                    ";
            $context["field_popup"] = (($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 = ($context["column_names"] ?? null)) && is_array($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4) || $__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 instanceof ArrayAccess ? ($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4[$context["column_index"]] ?? null) : null);
            // line 42
            echo "                    ";
            $context["foreign_data"] = call_user_func_array($this->env->getFunction('get_foreign_data')->getCallable(), [            // line 43
($context["foreigners"] ?? null),             // line 44
($context["field_popup"] ?? null), false, "", ""]);
            // line 49
            echo "                    <tr class=\"noclick\">
                        <th>";
            // line 50
            echo twig_escape_filter($this->env, (($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 = ($context["column_names"] ?? null)) && is_array($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144) || $__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144 instanceof ArrayAccess ? ($__internal_62824350bc4502ee19dbc2e99fc6bdd3bd90e7d8dd6e72f42c35efd048542144[$context["column_index"]] ?? null) : null), "html", null, true);
            echo "</th>
                        ";
            // line 52
            echo "                        <th>
                            ";
            // line 53
            if (((($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b = ($context["column_null_flags"] ?? null)) && is_array($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b) || $__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b instanceof ArrayAccess ? ($__internal_1cfccaec8dd2e8578ccb026fbe7f2e7e29ac2ed5deb976639c5fc99a6ea8583b[$context["column_index"]] ?? null) : null) == "YES")) {
                // line 54
                echo "                                <input type=\"checkbox\" class=\"checkbox_null\"
                                    name=\"criteriaColumnNullFlags[";
                // line 55
                echo twig_escape_filter($this->env, $context["column_index"], "html", null, true);
                echo "]\"
                                    id=\"edit_fields_null_id_";
                // line 56
                echo twig_escape_filter($this->env, $context["column_index"], "html", null, true);
                echo "\">
                            ";
            }
            // line 58
            echo "                        </th>
                        ";
            // line 60
            echo "                        <th>
                            ";
            // line 61
            $this->loadTemplate("table/search/input_box.twig", "table/search/zoom_result_form.twig", 61)->display(twig_to_array(["str" => "", "column_type" => (($__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002 =             // line 63
($context["column_types"] ?? null)) && is_array($__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002) || $__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002 instanceof ArrayAccess ? ($__internal_68aa442c1d43d3410ea8f958ba9090f3eaa9a76f8de8fc9be4d6c7389ba28002[$context["column_index"]] ?? null) : null), "column_id" => (((($__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4 =             // line 64
($context["column_types"] ?? null)) && is_array($__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4) || $__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4 instanceof ArrayAccess ? ($__internal_d7fc55f1a54b629533d60b43063289db62e68921ee7a5f8de562bd9d4a2b7ad4[$context["column_index"]] ?? null) : null)) ? ("edit_fieldID_") : ("fieldID_")), "in_zoom_search_edit" => true, "foreigners" =>             // line 66
($context["foreigners"] ?? null), "column_name" =>             // line 67
($context["field_popup"] ?? null), "column_name_hash" => (($__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666 =             // line 68
($context["column_name_hashes"] ?? null)) && is_array($__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666) || $__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666 instanceof ArrayAccess ? ($__internal_01476f8db28655ee4ee02ea2d17dd5a92599be76304f08cd8bc0e05aced30666[($context["field_popup"] ?? null)] ?? null) : null), "foreign_data" =>             // line 69
($context["foreign_data"] ?? null), "table" =>             // line 70
($context["table"] ?? null), "column_index" =>             // line 71
$context["column_index"], "foreign_max_limit" =>             // line 72
($context["foreign_max_limit"] ?? null), "criteria_values" => "", "db" =>             // line 74
($context["db"] ?? null), "titles" =>             // line 75
($context["titles"] ?? null), "in_fbs" => false]));
            // line 78
            echo "                        </th>
                    </tr>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['column_index'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 81
        echo "                </tbody>
            </table>
        </div>
        <input type=\"hidden\" id=\"queryID\" name=\"sql_query\">
    </fieldset>
</form>
";
    }

    public function getTemplateName()
    {
        return "table/search/zoom_result_form.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  175 => 81,  167 => 78,  165 => 75,  164 => 74,  163 => 72,  162 => 71,  161 => 70,  160 => 69,  159 => 68,  158 => 67,  157 => 66,  156 => 64,  155 => 63,  154 => 61,  151 => 60,  148 => 58,  143 => 56,  139 => 55,  136 => 54,  134 => 53,  131 => 52,  127 => 50,  124 => 49,  122 => 44,  121 => 43,  119 => 42,  116 => 41,  112 => 40,  105 => 36,  101 => 35,  97 => 34,  91 => 30,  87 => 27,  82 => 24,  80 => 23,  73 => 19,  68 => 16,  66 => 15,  61 => 12,  59 => 11,  56 => 10,  51 => 7,  44 => 3,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "table/search/zoom_result_form.twig", "/var/www/html/wp-content/plugins/wp-phpmyadmin-extension/lib/phpMyAdmin_0XEepCTyPBqKjzU9nJQMO7V/templates/table/search/zoom_result_form.twig");
    }
}
