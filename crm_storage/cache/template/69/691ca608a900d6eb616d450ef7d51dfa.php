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

/* erpadmin/view/template/masters/client_form.twig */
class __TwigTemplate_bd35724297b5e89208718b937c2be905 extends Template
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
        echo ($context["header"] ?? null);
        echo ($context["column_left"] ?? null);
        echo "
<div id=\"content\">
  <div class=\"page-header\">
    <div class=\"container-fluid\">
      <div class=\"float-end\">
        <button type=\"submit\" form=\"form-client\" data-bs-toggle=\"tooltip\" title=\"";
        // line 6
        echo ($context["button_save"] ?? null);
        echo "\" class=\"btn btn-primary\"><i class=\"fa-solid fa-floppy-disk\"></i></button>
        <a href=\"";
        // line 7
        echo ($context["back"] ?? null);
        echo "\" data-bs-toggle=\"tooltip\" title=\"";
        echo ($context["button_back"] ?? null);
        echo "\" class=\"btn btn-light\"><i class=\"fa-solid fa-reply\"></i></a></div>
      <h1>Client</h1>
      <ol class=\"breadcrumb\">
        ";
        // line 10
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["breadcrumbs"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["breadcrumb"]) {
            // line 11
            echo "          <li class=\"breadcrumb-item\"><a href=\"";
            echo twig_get_attribute($this->env, $this->source, $context["breadcrumb"], "href", [], "any", false, false, false, 11);
            echo "\">";
            echo twig_get_attribute($this->env, $this->source, $context["breadcrumb"], "text", [], "any", false, false, false, 11);
            echo "</a></li>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['breadcrumb'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 13
        echo "      </ol>
    </div>
  </div>
  <div class=\"container-fluid\">
    <div class=\"card\">
      <div class=\"card-header\"><i class=\"fa-solid fa-pencil\"></i> ";
        // line 18
        echo ($context["text_form"] ?? null);
        echo "</div>
      <div class=\"card-body\">
        <form id=\"form-client\" action=\"";
        // line 20
        echo ($context["action"] ?? null);
        echo "\" method=\"post\" data-oc-toggle=\"ajax\">
            <div class=\"row mb-3\">
              <label class=\"col-sm-1 col-form-label\">Name</label>
              <div class=\"col-sm-11\">
                  <div class=\"input-group\">
                    <input type=\"hidden\" name=\"client_status\" value=\"0\"/>
                    <input type=\"text\" name=\"client_name\" value=\"";
        // line 26
        echo ($context["name"] ?? null);
        echo "\" placeholder=\"Name\" id=\"client_name\" class=\"form-control\"/>
                  </div>
              </div>
            </div>
            <div class=\"row mb-3\">
              <label class=\"col-sm-1 col-form-label\">Status</label>
              <div class=\"col-sm-11\">
                  <div class=\"form-check form-switch form-switch-lg\">
                    <input type=\"checkbox\" name=\"client_status\" value=\"1\" id=\"client_status\" class=\"form-check-input\" ";
        // line 34
        if (($context["status"] ?? null)) {
            echo "checked";
        }
        echo "/>
                  </div>
              </div>
            </div>
          <input type=\"hidden\" name=\"client_id\" value=\"";
        // line 38
        echo ($context["client_id"] ?? null);
        echo "\" id=\"input-client-id\"/>
        </form>
      </div>
    </div>
  </div>
</div>
";
        // line 44
        echo ($context["footer"] ?? null);
    }

    public function getTemplateName()
    {
        return "erpadmin/view/template/masters/client_form.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  123 => 44,  114 => 38,  105 => 34,  94 => 26,  85 => 20,  80 => 18,  73 => 13,  62 => 11,  58 => 10,  50 => 7,  46 => 6,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "erpadmin/view/template/masters/client_form.twig", "C:\\laragon\\www\\jewellery\\erpadmin\\view\\template\\masters\\client_form.twig");
    }
}