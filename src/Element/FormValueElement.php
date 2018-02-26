<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Element;

use Ingenerator\Form\Util\FormDataArray;

interface FormValueElement
{

    public function assignValue(FormDataArray $data);

    public function collectValue(FormDataArray $data);

    public function assignErrors(FormDataArray $errors);


}
