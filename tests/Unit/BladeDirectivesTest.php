<?php

namespace Napp\Core\Acl\Tests\Unit;

use Napp\Core\Acl\Tests\Concerns\RendersBlade;
use Napp\Core\Acl\Tests\TestCase;

class BladeDirectivesTest extends TestCase
{
    use RendersBlade;

    public function test_may()
    {
        $string = '@may("users.create")
Have access
@endmay';

        $expected = '<?php if(may("users.create")): ?>
Have access
<?php endif; ?>';

        $this->assertEquals($expected, $this->compileBlade($string));
    }

    public function test_maynot()
    {
        $string = '@maynot("users.create")
Dont have access
@endmaynot';

        $expected = '<?php if(maynot("users.create")): ?>
Dont have access
<?php endif; ?>';

        $this->assertEquals($expected, $this->compileBlade($string));
    }

    public function test_mayall()
    {
        $string = '@mayall(["users.create", "users.update"])
Have access
@endmayall';

        $expected = '<?php if(mayall(["users.create", "users.update"])): ?>
Have access
<?php endif; ?>';

        $this->assertEquals($expected, $this->compileBlade($string));
    }

    public function test_hasrole()
    {
        $string = '@hasrole("manager")
I am manager
@endhasrole';

        $expected = '<?php if(has_role("manager")): ?>
I am manager
<?php endif; ?>';

        $this->assertEquals($expected, $this->compileBlade($string));
    }
}
