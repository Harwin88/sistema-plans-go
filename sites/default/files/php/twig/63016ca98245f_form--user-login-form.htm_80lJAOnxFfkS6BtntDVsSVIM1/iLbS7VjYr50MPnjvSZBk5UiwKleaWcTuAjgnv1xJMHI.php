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

/* themes/custom/theme_gobernacion/form/form--user-login-form.html.twig */
class __TwigTemplate_50e529ba0936d61f0de7952f7b63e6a2bdc28fba1681b1e23f53837ccdd62201 extends \Twig\Template
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
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "
<div class=\"container\">
    <form";
        // line 3
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["attributes"] ?? null), 3, $this->source), "html", null, true);
        echo ">
    <div class=\"row\">
        <div class=\"col-sm-8\">
        ";
        // line 6
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["element"] ?? null), "messages", [], "any", false, false, true, 6), 6, $this->source), "html", null, true);
        echo "
       </div>
   </div>
   <div class=\"row\">
   <div class=\"col-sm-8\">
    <div class=\"row\">
      <div class=\"col-sm-12\">";
        // line 12
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["element"] ?? null), "name", [], "any", false, false, true, 12), 12, $this->source), "html", null, true);
        echo " ";
        echo "</div>
    </div>
    <div class=\"row\">
      <div class=\"col-sm\"> ";
        // line 15
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["element"] ?? null), "pass", [], "any", false, false, true, 15), 15, $this->source), "html", null, true);
        echo " ";
        echo "</div>
    </div>
  </div>
  <div class=\"col-sm-4 \" style=\"text-align: initial;\">
    ";
        // line 19
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["element"] ?? null), "actions", [], "any", false, false, true, 19), "submit", [], "any", false, false, true, 19), 19, $this->source), "html", null, true);
        echo "
  </div>
  </div>
<div>
    <!--<div class=\"login-form__help_actions\">
        <input id=\"show_password\" class=\"show_password\" type=\"checkbox\" />
        <label class=\"password-toggle\" for=\"show_password\">Show password</label>
        <a href=\"/user/password\" class=\"forgot-password\">Forgot Password?</a>
    </div>-->
    ";
        // line 28
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["element"] ?? null), "form_build_id", [], "any", false, false, true, 28), 28, $this->source), "html", null, true);
        echo " ";
        // line 29
        echo "    ";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["element"] ?? null), "form_id", [], "any", false, false, true, 29), 29, $this->source), "html", null, true);
        echo " ";
        // line 30
        echo "</div>
</div>
</form>
</div>
";
    }

    public function getTemplateName()
    {
        return "themes/custom/theme_gobernacion/form/form--user-login-form.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  92 => 30,  88 => 29,  85 => 28,  73 => 19,  65 => 15,  58 => 12,  49 => 6,  43 => 3,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "themes/custom/theme_gobernacion/form/form--user-login-form.html.twig", "/home/u491346729/domains/sistemadeseguimientoproyectos.com/public_html/themes/custom/theme_gobernacion/form/form--user-login-form.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array();
        static $filters = array("escape" => 3);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                [],
                ['escape'],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
