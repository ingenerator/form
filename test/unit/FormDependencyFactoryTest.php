<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form;


use Ingenerator\Form\Element\Field\TextField;
use Ingenerator\Form\FormConfig;
use Ingenerator\Form\FormDependencyFactory;
use Ingenerator\Form\FormElementFactory;
use Ingenerator\Form\FormValidator;
use Ingenerator\Form\Renderer\FormEditRenderer;
use test\unit\Ingenerator\KohanaExtras\DependencyFactory\AbstractDependencyFactoryTest;

class FormDependencyFactoryTest extends AbstractDependencyFactoryTest
{

    public function test_it_defines_form_config()
    {
        $this->assertInstanceOf(FormConfig::class, $this->getService('form.config'));
    }

    public function test_its_defined_form_config_picks_up_overrides_from_application_config()
    {
        $cfg                                              = \Kohana::$config->load('form');
        $values                                           = $cfg->get('form_config');
        $values['element_type_map']['std']                = \stdClass::class;
        $values['template_map'][TextField::class]['edit'] = __FILE__;
        $cfg->set('form_config', $values);

        $config = $this->getService('form.config');
        /** @var FormConfig $config */
        $this->assertSame(\stdClass::class, $config->getElementClass('std'));
        $this->assertSame(__FILE__, $config->getTemplateFile(TextField::class, 'edit'));
    }

    public function test_it_defines_form_edit_renderer()
    {
        $this->assertInstanceOf(FormEditRenderer::class, $this->getService('form.renderer.edit'));
    }

    public function test_it_defines_form_display_renderer()
    {
        $this->assertInstanceOf(
            FormEditRenderer::class,
            $this->getService('form.renderer.display')
        );
    }

    public function test_it_defines_form_element_factory()
    {
        $this->assertInstanceOf(
            FormElementFactory::class,
            $this->getService('form.element_factory')
        );
    }

    public function test_it_defines_form_validator()
    {
        $this->assertInstanceOf(FormValidator::class, $this->getService('form.validator'));
    }

    protected function getService($service)
    {
        return $this->assertDefinesService($service, FormDependencyFactory::definitions());
    }
}
