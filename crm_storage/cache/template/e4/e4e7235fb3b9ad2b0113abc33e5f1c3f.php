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

/* erpadmin/view/template/catalog/category_list.twig */
class __TwigTemplate_61750ab0a4b465ad70c5cdbf926c8a7d extends Template
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
        echo "<form id=\"form-category\" method=\"post\" data-oc-toggle=\"ajax\" data-oc-load=\"";
        echo ($context["action"] ?? null);
        echo "\" data-oc-target=\"#category\">
  <div class=\"table-responsive\">
    <table class=\"table table-bordered table-hover\">
      <thead>
        <tr>
          <td class=\"text-center\" style=\"width: 1px;\"><input type=\"checkbox\" onclick=\"\$('input[name*=\\'selected\\']').prop('checked', \$(this).prop('checked'));\" class=\"form-check-input\"/></td>
          <td class=\"text-start\"><a href=\"";
        // line 7
        echo ($context["sort_name"] ?? null);
        echo "\"";
        if ((($context["sort"] ?? null) == "name")) {
            echo " class=\"";
            echo twig_lower_filter($this->env, ($context["order"] ?? null));
            echo "\"";
        }
        echo ">";
        echo ($context["column_name"] ?? null);
        echo "</a></td>
          <td class=\"text-end\"><a href=\"";
        // line 8
        echo ($context["sort_sort_order"] ?? null);
        echo "\"";
        if ((($context["sort"] ?? null) == "sort_order")) {
            echo " class=\"";
            echo twig_lower_filter($this->env, ($context["order"] ?? null));
            echo "\"";
        }
        echo ">";
        echo ($context["column_sort_order"] ?? null);
        echo "</a></td>
          <td class=\"text-end\">";
        // line 9
        echo ($context["column_action"] ?? null);
        echo "</td>
        </tr>
      </thead>
      <tbody>
        ";
        // line 13
        if (($context["categories"] ?? null)) {
            // line 14
            echo "          ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["categories"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["category"]) {
                // line 15
                echo "            <tr>
              <td class=\"text-center\"><input type=\"checkbox\" name=\"selected[]\" value=\"";
                // line 16
                echo twig_get_attribute($this->env, $this->source, $context["category"], "category_id", [], "any", false, false, false, 16);
                echo "\" class=\"form-check-input\"/></td>
              <td class=\"text-start\">";
                // line 17
                echo twig_get_attribute($this->env, $this->source, $context["category"], "name", [], "any", false, false, false, 17);
                echo "
                <br/>
                ";
                // line 19
                if (twig_get_attribute($this->env, $this->source, $context["category"], "status", [], "any", false, false, false, 19)) {
                    // line 20
                    echo "                  <small class=\"text-success\">";
                    echo ($context["text_enabled"] ?? null);
                    echo "</small>
                ";
                } else {
                    // line 22
                    echo "                  <small class=\"text-danger\">";
                    echo ($context["text_disabled"] ?? null);
                    echo "</small>
                ";
                }
                // line 23
                echo "</td>
              <td class=\"text-end\">";
                // line 24
                echo twig_get_attribute($this->env, $this->source, $context["category"], "sort_order", [], "any", false, false, false, 24);
                echo "</td>
              <td class=\"text-end\"><a href=\"";
                // line 25
                echo twig_get_attribute($this->env, $this->source, $context["category"], "edit", [], "any", false, false, false, 25);
                echo "\" data-bs-toggle=\"tooltip\" title=\"";
                echo ($context["button_edit"] ?? null);
                echo "\" class=\"btn btn-primary\"><i class=\"fa-solid fa-pencil\"></i></a></td>
            </tr>
          ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['category'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 28
            echo "        ";
        } else {
            // line 29
            echo "          <tr>
            <td class=\"text-center\" colspan=\"4\">";
            // line 30
            echo ($context["text_no_results"] ?? null);
            echo "</td>
          </tr>
        ";
        }
        // line 33
        echo "      </tbody>
    </table>
  </div>
  <div class=\"row\">
    <div class=\"col-sm-6 text-start\">";
        // line 37
        echo ($context["pagination"] ?? null);
        echo "</div>
    <div class=\"col-sm-6 text-end\">";
        // line 38
        echo ($context["results"] ?? null);
        echo "</div>
  </div>
</form>

";
    }

    public function getTemplateName()
    {
        return "erpadmin/view/template/catalog/category_list.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  151 => 38,  147 => 37,  141 => 33,  135 => 30,  132 => 29,  129 => 28,  118 => 25,  114 => 24,  111 => 23,  105 => 22,  99 => 20,  97 => 19,  92 => 17,  88 => 16,  85 => 15,  80 => 14,  78 => 13,  71 => 9,  59 => 8,  47 => 7,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "erpadmin/view/template/catalog/category_list.twig", "C:\\laragon\\www\\jewellery\\erpadmin\\view\\template\\catalog\\category_list.twig");
    }
}
