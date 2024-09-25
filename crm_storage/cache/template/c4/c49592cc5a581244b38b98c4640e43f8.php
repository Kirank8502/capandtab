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

/* erpadmin/view/template/masters/shape_list.twig */
class __TwigTemplate_10a552927394562d46c4e4288c784f25 extends Template
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
        echo "<form id=\"form-shape\" method=\"post\" data-oc-toggle=\"ajax\" data-oc-load=\"";
        echo ($context["action"] ?? null);
        echo "\" data-oc-target=\"#shape\">
  <div class=\"table-responsive\">
    <table class=\"table table-bordered table-hover\">
      <thead>
        <tr>
          <td class=\"text-center\" style=\"width: 1px;\"><input type=\"checkbox\" onclick=\"\$('input[name*=\\'selected\\']').prop('checked', \$(this).prop('checked'));\" class=\"form-check-input\"/></td>
          <td class=\"text-center\">Name</td>
          <td class=\"text-center\">Status</td>
          <td class=\"text-end\">Action</td>
        </tr>
      </thead>
      <tbody>
        ";
        // line 13
        if (($context["shapes"] ?? null)) {
            // line 14
            echo "          ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["shapes"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["shape"]) {
                // line 15
                echo "            <tr>
              <td class=\"text-center\"><input type=\"checkbox\" name=\"selected[]\" value=\"";
                // line 16
                echo twig_get_attribute($this->env, $this->source, $context["shape"], "shape_id", [], "any", false, false, false, 16);
                echo "\" class=\"form-check-input\"/></td>
              <td class=\"text-center\">";
                // line 17
                echo twig_get_attribute($this->env, $this->source, $context["shape"], "name", [], "any", false, false, false, 17);
                echo "</td>
              <td class=\"text-center\">";
                // line 18
                if (twig_get_attribute($this->env, $this->source, $context["shape"], "status", [], "any", false, false, false, 18)) {
                    echo "Enabled";
                } else {
                    echo "Disabled";
                }
                echo "</td>
              <td class=\"text-end\">
                <a href=\"";
                // line 20
                echo twig_get_attribute($this->env, $this->source, $context["shape"], "edit", [], "any", false, false, false, 20);
                echo "\" data-bs-toggle=\"tooltip\" title=\"Edit\" class=\"btn btn-warning\"><i class=\"fa-solid fa-pencil\"></i></a>
              </td>
            </tr>
          ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['shape'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 24
            echo "        ";
        } else {
            // line 25
            echo "          <tr>
            <td class=\"text-center\" colspan=\"7\">";
            // line 26
            echo ($context["text_no_results"] ?? null);
            echo "</td>
          </tr>
        ";
        }
        // line 29
        echo "      </tbody>
    </table>
  </div>
  <div class=\"row\">
    <div class=\"col-sm-6 text-start\">";
        // line 33
        echo ($context["pagination"] ?? null);
        echo "</div>
    <div class=\"col-sm-6 text-end\">";
        // line 34
        echo ($context["results"] ?? null);
        echo "</div>
  </div>
</form>
";
    }

    public function getTemplateName()
    {
        return "erpadmin/view/template/masters/shape_list.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  112 => 34,  108 => 33,  102 => 29,  96 => 26,  93 => 25,  90 => 24,  80 => 20,  71 => 18,  67 => 17,  63 => 16,  60 => 15,  55 => 14,  53 => 13,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "erpadmin/view/template/masters/shape_list.twig", "C:\\laragon\\www\\jewellery\\erpadmin\\view\\template\\masters\\shape_list.twig");
    }
}