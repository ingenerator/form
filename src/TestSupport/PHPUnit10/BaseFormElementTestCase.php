<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\TestSupport\PHPUnit10;


use DomainException;
use Ingenerator\Form\Element\AbstractFormElement;
use Ingenerator\Form\Element\Field\AbstractFormField;
use Ingenerator\Form\Element\FormValueElement;
use Ingenerator\Form\FormConfig;
use Ingenerator\Form\FormElementFactory;
use Ingenerator\Form\Util\FormDataArray;
use InvalidArgumentException;
use LogicException;
use OutOfBoundsException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function get_class;

abstract class BaseFormElementTestCase extends TestCase
{

    public static function provider_required_options(): array
    {
        return [];
    }

    public static function provider_valid_options_and_defaults(): array
    {
        return [];
    }

    #[DataProvider('provider_required_options')]
    public function test_it_cannot_be_constructed_without_required_options($option)
    {
        $this->expectException(DomainException::class);
        $this->newSubject([$option => '']);
    }

    public function test_it_cannot_be_constructed_with_invalid_options()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->newSubject(['some-old-nonsense' => 'junk']);
    }

    public function test_it_throws_on_access_to_unknown_property()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->newSubject()->some_old_nonsense;
    }

    public function test_it_throws_on_attempt_to_assign_any_property()
    {
        $field = 'name';
        $this->expectException(LogicException::class);
        $this->newSubject()->$field = 'cannot-do-this';
    }

    #[DataProvider('provider_valid_options_and_defaults')]
    public function test_it_supports_all_expected_options_with_defaults(
        $option,
        $expect_default,
        $custom_val
    ) {
        $this->assertSame(
            $expect_default,
            $this->newSubject()->$option,
            'Provides expected default option'
        );
        $this->assertSame(
            $custom_val,
            $this->newSubject([$option => $custom_val])->$option,
            'Provides expected custom option'
        );
    }

    public function test_it_ignores_comments()
    {
        // NB: removing this from the array might be problematic if we want to persist a collection
        // of field schemas back to an array....
        $subject = $this->newSubject(['_comment' => 'Some info to make the schema make sense']);
        $this->assertDoesNotExposeProperty($subject, '_comment');
    }

    public function test_it_ignores_type_schema()
    {
        // NB: removing this from the array might be problematic if we want to persist a collection
        // of field schemas back to an array....
        $subject = $this->newSubject(['type' => 'text']);
        $this->assertDoesNotExposeProperty($subject, 'type');
    }

    abstract protected function newSubject(array $values = []): AbstractFormElement;

    /**
     * @param array               $expect
     * @param AbstractFormField[] $elements
     */
    protected function assertFieldCollectionEquals(array $expect, array $elements): void
    {
        $actual = [];
        foreach ($elements as $index => $field) {
            $actual[$index] = [
                'class' => get_class($field),
                'name'  => $field->name,
                'value' => $field->html_value
            ];
        }
        $this->assertEquals($expect, $actual);
    }

    protected function assertCollectsValues(array $expect, FormValueElement $subject): void
    {
        $data = new FormDataArray([]);
        $subject->collectValue($data);
        $this->assertSame($expect, $data->getValues());
    }

    protected function getElementFactory(): FormElementFactory
    {
        return new FormElementFactory(FormConfig::withDefaults());
    }

    protected function assertDoesNotExposeProperty(AbstractFormElement $element, string $property): void
    {
        $e = NULL;
        try {
            $foo = $element->$property;
        } catch (OutOfBoundsException $e) {
            // Expected
        }
        $this->assertInstanceOf(
            OutOfBoundsException::class,
            $e,
            'Should throw on $element->'.$property
        );
    }
}
