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

/* themes/custom/theme_gobernacion/templates/page--user--login.html.twig */
class __TwigTemplate_7ba4f45ff064255732847d76709d2cacd3da333541f225779e9bf3261310c50a extends \Twig\Template
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
<div class='header-login'>
<div class=\"container\">
  <div class=\"row\">
    <div class=\"col-sm-3\">
      <img class=\"pla-imgWW imagen-bgWW\" src=\"../../../../themes/custom/theme_gobernacion/assets/imagen/LOGO GOBERNACION 2020-2023 SIN FONDO.png\" width=\"336px\" alt=\"Plan de desarrollo\">
    </div>
    <div class=\"col-sm-9\">
      <h4 class=\"title-g\">SISTEMA DE SEGUIMIENTO A LOS PROYECTOS DE INVERSIÓN DE LA SECRETARIA DE INFRAESTRUCTURA</h4>
    </div>
  </div>
</div>
</div>

<div class=\"container conten-form-login\">
    <div class=\"row align-items-center\">
      <div class=\"col\">
        <img class=\"pla-img imagen-bg\" src=\"../../../../themes/custom/theme_gobernacion/assets/imagen/image7.jpg\" width=\"336px\" alt=\"Plan de desarrollo\">
      </div>
      <div class=\"col conten-icons\">
        <div class=\"row\">
          <div class=\"col-sm-7\">
            <img class=\"pla-img\" src=\"../../../../themes/custom/theme_gobernacion/assets/imagen/image1.png\" width=\"88px\" alt=\"Plan de desarrollo\">
          </div>
          <div class=\"col-sm-3\">
            <p class=\"text-aline-elem\">Plan de desarrollo</p>
          </div>
        </div>
        <div class=\"row\">
          <div class=\"col-sm-6\">
            <img class=\"pla-img\" src=\"../../../../themes/custom/theme_gobernacion/assets/imagen/image2.png\" width=\"88px\" alt=\"Plan de desarrollo\">
          </div>
          <div class=\"col-sm-4\">
            <p class=\"text-aline-elem\">Presupuesto</p>
          </div>
        </div>
        <div class=\"row\">
          <div class=\"col-sm-5\">
            <img class=\"pla-img\" src=\"../../../../themes/custom/theme_gobernacion/assets/imagen/image3.png\" width=\"88px\" alt=\"Plan de desarrollo\">
          </div>
          <div class=\"col-sm-7\">
            <p class=\"text-aline-elem\">Proyectos</p>
          </div>
        </div>
        <div class=\"row\">
          <div class=\"col-sm-5\">
            <img class=\"pla-img\" src=\"../../../../themes/custom/theme_gobernacion/assets/imagen/image4.png\" width=\"88px\" alt=\"Plan de desarrollo\">
          </div>
          <div class=\"col-sm-7\">
            <p class=\"text-aline-elem\">Actividades</p>
          </div>
        </div>
        <div class=\"row\">
          <div class=\"col-sm-6\">
            <img class=\"pla-img\" src=\"../../../../themes/custom/theme_gobernacion/assets/imagen/image5.png\" width=\"88px\" alt=\"Plan de desarrollo\">
          </div>
          <div class=\"col-sm-4\">
            <p class=\"text-aline-elem\">contratación</p>
          </div>
        </div>
        <div class=\"row\">
          <div class=\"col-sm-7\">
            <img class=\"pla-img\" src=\"../../../../themes/custom/theme_gobernacion/assets/imagen/image6.png\" width=\"88px\" alt=\"Plan de desarrollo\">
          </div>
          <div class=\"col-sm-5\">
            <p class=\"text-aline-elem\">Actas</p>
          </div>
        </div>
      </div>
    <div class=\"col alint-form\">
          ";
        // line 71
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "content", [], "any", false, false, true, 71), 71, $this->source), "html", null, true);
        echo "
    </div>
    </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "themes/custom/theme_gobernacion/templates/page--user--login.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  111 => 71,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "themes/custom/theme_gobernacion/templates/page--user--login.html.twig", "/home/u491346729/domains/sistemadeseguimientoproyectos.com/public_html/themes/custom/theme_gobernacion/templates/page--user--login.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array();
        static $filters = array("escape" => 71);
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
