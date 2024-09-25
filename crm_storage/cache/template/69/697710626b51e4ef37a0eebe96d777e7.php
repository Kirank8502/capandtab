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

/* erpadmin/view/template/masters/colour_list.twig */
class __TwigTemplate_005ebd16da2e5ca121c01cea9db8a1b9 extends Template
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
        echo "<form id=\"form-colour\" method=\"post\" data-oc-toggle=\"ajax\" data-oc-load=\"";
        echo ($context["action"] ?? null);
        echo "\" data-oc-target=\"#colour\">
  <div class=\"table-responsive\">
    <table class=\"table table-bordered table-hover\">
      <thead>
        <tr>
          <td class=\"text-center\" style=\"width: 1px;\"><input type=\"checkbox\" onclick=\"\$('input[name*=\\'selected\\']').prop('checked', \$(this).prop('checked'));\" class=\"form-check-input\"/></td>
          <td class=\"text-center\">Name</td>
          <td class=\"text-center\">Qty</td>
          <td class=\"text-center\">Status</td>
          <td class=\"text-end\">Action</td>
        </tr>
      </thead>
      <tbody>
        ";
        // line 14
        if (($context["colours"] ?? null)) {
            // line 15
            echo "          ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["colours"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["colour"]) {
                // line 16
                echo "            <tr>
              <td class=\"text-center\"><input type=\"checkbox\" name=\"selected[]\" value=\"";
                // line 17
                echo twig_get_attribute($this->env, $this->source, $context["colour"], "colour_id", [], "any", false, false, false, 17);
                echo "\" class=\"form-check-input\"/></td>
              <td class=\"text-center\">";
                // line 18
                echo twig_get_attribute($this->env, $this->source, $context["colour"], "name", [], "any", false, false, false, 18);
                echo "</td>
              <td class=\"text-center\">";
                // line 19
                echo twig_get_attribute($this->env, $this->source, $context["colour"], "qty", [], "any", false, false, false, 19);
                echo "</td>
              <td class=\"text-center\">";
                // line 20
                if (twig_get_attribute($this->env, $this->source, $context["colour"], "status", [], "any", false, false, false, 20)) {
                    echo "Enabled";
                } else {
                    echo "Disabled";
                }
                echo "</td>
              <td class=\"text-end\">
                <a href=\"";
                // line 22
                echo twig_get_attribute($this->env, $this->source, $context["colour"], "edit", [], "any", false, false, false, 22);
                echo "\" data-bs-toggle=\"tooltip\" title=\"Edit\" class=\"btn btn-warning\"><i class=\"fa-solid fa-pencil\"></i></a>
              </td>
            </tr>
          ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['colour'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 26
            echo "        ";
        } else {
            // line 27
            echo "          <tr>
            <td class=\"text-center\" colspan=\"7\">";
            // line 28
            echo ($context["text_no_results"] ?? null);
            echo "</td>
          </tr>
        ";
        }
        // line 31
        echo "      </tbody>
    </table>
  </div>
  <div class=\"row\">
    <div class=\"col-sm-6 text-start\">";
        // line 35
        echo ($context["pagination"] ?? null);
        echo "</div>
    <div class=\"col-sm-6 text-end\">";
        // line 36
        echo ($context["results"] ?? null);
        echo "</div>
  </div>
</form>
";
    }

    public function getTemplateName()
    {
        return "erpadmin/view/template/masters/colour_list.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  117 => 36,  113 => 35,  107 => 31,  101 => 28,  98 => 27,  95 => 26,  85 => 22,  76 => 20,  72 => 19,  68 => 18,  64 => 17,  61 => 16,  56 => 15,  54 => 14,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "erpadmin/view/template/masters/colour_list.twig", "C:\\laragon\\www\\jewellery\\erpadmin\\view\\template\\masters\\colour_list.twig");
    }
}
