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

/* erpadmin/view/template/catalog/order_form.twig */
class __TwigTemplate_f56b65340269d0a9cdff8b0f7e488ad9 extends Template
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
        <button type=\"submit\" form=\"form-order\" data-bs-toggle=\"tooltip\" title=\"";
        // line 6
        echo ($context["button_save"] ?? null);
        echo "\" class=\"btn btn-primary\"><i class=\"fa-solid fa-floppy-disk\"></i></button>
        <a href=\"";
        // line 7
        echo ($context["back"] ?? null);
        echo "\" data-bs-toggle=\"tooltip\" title=\"";
        echo ($context["button_back"] ?? null);
        echo "\" class=\"btn btn-light\"><i class=\"fa-solid fa-reply\"></i></a></div>
      <h1>Order</h1>
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
        <form id=\"form-order\" action=\"";
        // line 20
        echo ($context["action"] ?? null);
        echo "\" method=\"post\" data-oc-toggle=\"ajax\">
            <div class=\"row mb-3\">
              <label class=\"col-sm-1 col-form-label\">Name</label>
              <div class=\"col-sm-11\">
                  <div class=\"input-group\">
                    <input type=\"text\" name=\"order_name\" value=\"";
        // line 25
        echo ($context["name"] ?? null);
        echo "\" placeholder=\"Name\" id=\"order_name\" class=\"form-control\"/>
                  </div>
              </div>
            </div>
            <div class=\"row mb-3\">
              <label class=\"col-sm-1 col-form-label\">Product</label>
              <div class=\"col-sm-11\">
                  <div class=\"input-group\">
                   <select name=\"product_id\" id=\"input-product-class\" class=\"form-select\">
                        <option value=\"0\">None</option>
                        ";
        // line 35
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["products"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
            // line 36
            echo "                          <option value=\"";
            echo twig_get_attribute($this->env, $this->source, $context["product"], "product_id", [], "any", false, false, false, 36);
            echo "\"";
            if ((twig_get_attribute($this->env, $this->source, $context["product"], "product_id", [], "any", false, false, false, 36) == ($context["product_id"] ?? null))) {
                echo " selected";
            }
            echo ">";
            echo twig_get_attribute($this->env, $this->source, $context["product"], "name", [], "any", false, false, false, 36);
            echo "</option>
                        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['product'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 38
        echo "                      </select>
                  </div>
              </div>
            </div>
            <div class=\"row mb-3\">
              <label class=\"col-sm-1 col-form-label\">Client</label>
              <div class=\"col-sm-11\">
                  <div class=\"input-group\">
                   <select name=\"client_id\" id=\"input-client-class\" class=\"form-select\">
                        <option value=\"0\">None</option>
                        ";
        // line 48
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["clients"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["client"]) {
            // line 49
            echo "                          <option value=\"";
            echo twig_get_attribute($this->env, $this->source, $context["client"], "client_id", [], "any", false, false, false, 49);
            echo "\"";
            if ((twig_get_attribute($this->env, $this->source, $context["client"], "client_id", [], "any", false, false, false, 49) == ($context["client_id"] ?? null))) {
                echo " selected";
            }
            echo ">";
            echo twig_get_attribute($this->env, $this->source, $context["client"], "name", [], "any", false, false, false, 49);
            echo "</option>
                        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['client'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 51
        echo "                      </select>
                  </div>
              </div>
            </div>
            <div class=\"row mb-3\">
              <label class=\"col-sm-1 col-form-label\">Powder</label>
              <div class=\"col-sm-11\">
                  <div class=\"input-group\">
                   <select name=\"powder_id\" id=\"input-powder-class\" class=\"form-select\">
                        <option value=\"0\">None</option>
                        ";
        // line 61
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["powders"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["powder"]) {
            // line 62
            echo "                          <option value=\"";
            echo twig_get_attribute($this->env, $this->source, $context["powder"], "powder_id", [], "any", false, false, false, 62);
            echo "\"";
            if ((twig_get_attribute($this->env, $this->source, $context["powder"], "powder_id", [], "any", false, false, false, 62) == ($context["powder_id"] ?? null))) {
                echo " selected";
            }
            echo ">";
            echo twig_get_attribute($this->env, $this->source, $context["powder"], "name", [], "any", false, false, false, 62);
            echo "</option>
                        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['powder'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 64
        echo "                      </select>
                  </div>
              </div>
            </div>
            <div class=\"row mb-3\">
              <label class=\"col-sm-1 col-form-label\">Colour</label>
              <div class=\"col-sm-11\">
                  <div class=\"input-group\">
                   <select name=\"colour_id\" id=\"input-colour-class\" class=\"form-select\">
                        <option value=\"0\">None</option>
                        ";
        // line 74
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["colours"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["colour"]) {
            // line 75
            echo "                          <option value=\"";
            echo twig_get_attribute($this->env, $this->source, $context["colour"], "colour_id", [], "any", false, false, false, 75);
            echo "\"";
            if ((twig_get_attribute($this->env, $this->source, $context["colour"], "colour_id", [], "any", false, false, false, 75) == ($context["colour_id"] ?? null))) {
                echo " selected";
            }
            echo ">";
            echo twig_get_attribute($this->env, $this->source, $context["colour"], "name", [], "any", false, false, false, 75);
            echo "</option>
                        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['colour'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 77
        echo "                      </select>
                  </div>
              </div>
            </div>
            <div class=\"row mb-3\">
              <label class=\"col-sm-1 col-form-label\">Address</label>
              <div class=\"col-sm-11\">
                  <div class=\"input-group\">
                      <input type=\"text\" name=\"address\" value=\"";
        // line 85
        echo ($context["address"] ?? null);
        echo "\" placeholder=\"Address\" id=\"input-address\" class=\"form-control\"/>
                    ";
        // line 87
        echo "                  </div>
              </div>
            </div>
            <div class=\"row mb-3\">
              <label class=\"col-sm-1 col-form-label\">Remark</label>
              <div class=\"col-sm-11\">
                  <div class=\"input-group\">
                      <input type=\"text\" name=\"Remark\" value=\"";
        // line 94
        echo ($context["remark"] ?? null);
        echo "\" placeholder=\"Remark\" id=\"input-remark\" class=\"form-control\"/>
                  </div>
              </div>
            </div>
            <div class=\"row mb-3\">
              <label class=\"col-sm-1 col-form-label\">Status</label>
              <div class=\"col-sm-11\">
                  <div class=\"form-check form-switch form-switch-lg\">
                    <input type=\"hidden\" name=\"order_status\" value=\"0\"/>
                    <input type=\"checkbox\" name=\"order_status\" value=\"1\" id=\"order_status\" class=\"form-check-input\" ";
        // line 103
        if (($context["status"] ?? null)) {
            echo "checked";
        }
        echo "/>
                  </div>
              </div>
            </div>
          <input type=\"hidden\" name=\"order_id\" value=\"";
        // line 107
        echo ($context["order_id"] ?? null);
        echo "\" id=\"input-order-id\"/>
        </form>
      </div>
    </div>
  </div>
</div>
";
        // line 113
        echo ($context["footer"] ?? null);
    }

    public function getTemplateName()
    {
        return "erpadmin/view/template/catalog/order_form.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  271 => 113,  262 => 107,  253 => 103,  241 => 94,  232 => 87,  228 => 85,  218 => 77,  203 => 75,  199 => 74,  187 => 64,  172 => 62,  168 => 61,  156 => 51,  141 => 49,  137 => 48,  125 => 38,  110 => 36,  106 => 35,  93 => 25,  85 => 20,  80 => 18,  73 => 13,  62 => 11,  58 => 10,  50 => 7,  46 => 6,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "erpadmin/view/template/catalog/order_form.twig", "C:\\laragon\\www\\jewellery\\erpadmin\\view\\template\\catalog\\order_form.twig");
    }
}
