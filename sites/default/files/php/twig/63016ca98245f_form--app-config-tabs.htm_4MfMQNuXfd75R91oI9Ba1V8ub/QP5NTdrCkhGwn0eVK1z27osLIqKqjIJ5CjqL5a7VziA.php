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

/* themes/custom/theme_gobernacion/form/form--app-config-tabs.html.twig */
class __TwigTemplate_5837b4bce0318550c128477b6d2bf2ad041a2ca6be950fbd71ac2e1899cac6df extends \Twig\Template
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
<div class=\" fcontainerorm-plan\">
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
      <div class=\"col \" >
         ";
        // line 12
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["element"] ?? null), 12, $this->source), "html", null, true);
        echo "
       </div>
    </div>

  </div>
  </div>
<div>

    ";
        // line 20
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["form"] ?? null), "form", [], "any", false, false, true, 20), "timezone", [], "any", false, false, true, 20), 20, $this->source), "html", null, true);
        echo "
    ";
        // line 21
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["form"] ?? null), "form_build_id", [], "any", false, false, true, 21), 21, $this->source), "html", null, true);
        echo "
    ";
        // line 22
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["form"] ?? null), "form_id", [], "any", false, false, true, 22), 22, $this->source), "html", null, true);
        echo "
    ";
        // line 23
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["form"] ?? null), "form", [], "any", false, false, true, 23), "form_token", [], "any", false, false, true, 23), 23, $this->source), "html", null, true);
        echo "
    ";
        // line 24
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["form"] ?? null), "form", [], "any", false, false, true, 24), "actions", [], "any", false, false, true, 24), 24, $this->source), "html", null, true);
        echo "
</div>
</div>
</form>
</div>
";
    }

    public function getTemplateName()
    {
        return "themes/custom/theme_gobernacion/form/form--app-config-tabs.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  85 => 24,  81 => 23,  77 => 22,  73 => 21,  69 => 20,  58 => 12,  49 => 6,  43 => 3,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "themes/custom/theme_gobernacion/form/form--app-config-tabs.html.twig", "/home/u491346729/domains/sistemadeseguimientoproyectos.com/public_html/themes/custom/theme_gobernacion/form/form--app-config-tabs.html.twig");
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
