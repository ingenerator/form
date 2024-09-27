<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form;


use Dependency_Container;
use Dependency_Definition_List;
use Dependency_Exception;
use Exception;
use Ingenerator\Form\Element\Field\TextField;
use Ingenerator\Form\FormConfig;
use Ingenerator\Form\FormDependencyFactory;
use Ingenerator\Form\FormElementFactory;
use Ingenerator\Form\FormValidator;
use Ingenerator\Form\Renderer\FormEditRenderer;
use Kohana;
use PHPUnit\Framework\TestCase;
use stdClass;
use function get_class;

class FormDependencyFactoryTest extends TestCase
{

    public function test_it_defines_form_config()
    {
        $this->assertInstanceOf(FormConfig::class, $this->getService('form.config'));
    }

    public function test_its_defined_form_config_picks_up_overrides_from_application_config()
    {
        $cfg                                              = Kohana::$config->load('form');
        $values                                           = $cfg->get('form_config');
        $values['element_type_map']['std']                = stdClass::class;
        $values['template_map'][TextField::class]['edit'] = __FILE__;
        $cfg->set('form_config', $values);

        $config = $this->getService('form.config');
        /** @var FormConfig $config */
        $this->assertSame(stdClass::class, $config->getElementClass('std'));
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
        // borrowed from kohana-extras AbstractDependencyFactoryTest
        $list = Dependency_Definition_List::factory()->from_array(FormDependencyFactory::definitions());
        try {
            $list->get($service);
        } catch (Exception $e) {
            $this->fail('Service `'.$service.'` is not defined: ['.get_class($e).'] '.$e->getMessage());
        }

        $container = new Dependency_Container($list);

        try {
            return $container->get($service);
        } catch (Dependency_Exception $e) {
            $this->fail('Cannot instantiate service `'.$service.'` - missing dependency? : '.$e->getMessage());
        } catch (Exception $e) {
            $this->fail('Cannot instantiate service `'.$service.'`: ['.get_class($e).'] '.$e->getMessage());
        }
    }
}
