<?php

namespace Napp\Core\Acl\Tests\Unit;

use Napp\Core\Acl\Tests\Concerns\RendersBlade;
use Napp\Core\Acl\Tests\TestCase;

class BladeDirectivesTest extends TestCase
{
    use RendersBlade;

    public function test_may()
    {
        $string = '@may("users.crete")
Have access
@endmay';

        $expected = '<?php if(acl()->may("users.crete")): ?>
Have access
<?php endif; ?>';

        $this->assertEquals($expected, $this->compileBlade($string));
    }

    public function test_maynot()
    {
        $string = '@maynot("users.crete")
Dont have access
@endmaynot';

        $expected = '<?php if(acl()->maynot("users.crete")): ?>
Dont have access
<?php endif; ?>';

        $this->assertEquals($expected, $this->compileBlade($string));
    }

    public function test_hasrole()
    {
        $string = '@hasrole("manager")
I am manager
@endhasrole';

        $expected = '<?php if(acl()->userHasRole("manager")): ?>
I am manager
<?php endif; ?>';

        $this->assertEquals($expected, $this->compileBlade($string));
    }

}