<?php

namespace Napp\Core\Acl;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

/**
 * Class AclServiceProvider.
 */
class AclServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->bind(AclServiceInterface::class, AclService::class);

        $this->registerBladeDirectives();
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__.'/../database/migrations' => base_path('database/migrations')], 'migrations');
        $this->publishes([__DIR__.'/../config/acl.php' => config_path('acl.php')], 'config');
    }

    /**
     * Register Blade directives.
     *
     * @return void
     */
    protected function registerBladeDirectives()
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
            $bladeCompiler->directive('hasrole', function ($role) {
                return "<?php if(has_role({$role})): ?>";
            });
            $bladeCompiler->directive('endhasrole', function () {
                return '<?php endif; ?>';
            });

            $bladeCompiler->directive('may', function ($permission) {
                return "<?php if(may({$permission})): ?>";
            });
            $bladeCompiler->directive('endmay', function () {
                return '<?php endif; ?>';
            });

            $bladeCompiler->directive('maynot', function ($permission) {
                return "<?php if(maynot({$permission})): ?>";
            });
            $bladeCompiler->directive('endmaynot', function () {
                return '<?php endif; ?>';
            });

            $bladeCompiler->directive('mayall', function ($permissions) {
                return "<?php if(mayall({$permissions})): ?>";
            });
            $bladeCompiler->directive('endmayall', function () {
                return '<?php endif; ?>';
            });
        });
    }
}
