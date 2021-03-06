<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Oneup\UploaderBundle\OneupUploaderBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new FOS\ElasticaBundle\FOSElasticaBundle(),
            new Braincrafted\Bundle\BootstrapBundle\BraincraftedBootstrapBundle(),
            new Problematic\AclManagerBundle\ProblematicAclManagerBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\AopBundle\JMSAopBundle(),
            new APY\DataGridBundle\APYDataGridBundle(),
            new PUGX\AutocompleterBundle\PUGXAutocompleterBundle(),
            new SC\DatetimepickerBundle\SCDatetimepickerBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new A2lix\TranslationFormBundle\A2lixTranslationFormBundle(),
            new Prezent\Doctrine\TranslatableBundle\PrezentDoctrineTranslatableBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Norzechowicz\AceEditorBundle\NorzechowiczAceEditorBundle(),
            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new Jb\Bundle\FileUploaderBundle\JbFileUploaderBundle(),
            new Okulbilisim\OjsToolsBundle\OkulbilisimOjsToolsBundle(),
            new Okulbilisim\FeedbackBundle\OkulbilisimFeedbackBundle(),
            new Ojs\CoreBundle\OjsCoreBundle(),
            new Ojs\SiteBundle\OjsSiteBundle(),
            new Ojs\AdminBundle\OjsAdminBundle(),
            new Ojs\SearchBundle\OjsSearchBundle(),
            new Ojs\ApiBundle\OjsApiBundle(),
            new Ojs\CliBundle\OjsCliBundle(),
            new Ojs\JournalBundle\OjsJournalBundle(),
            new Ojs\UserBundle\OjsUserBundle(),
            new Ojs\OAIBundle\OjsOAIBundle(),
            new Ojs\LocationBundle\OjsLocationBundle(),
            new Ojs\InstallerBundle\OjsInstallerBundle(),
            new Ojs\CmsBundle\OjsCmsBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Ojs\AnalyticsBundle\OjsAnalyticsBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new JMS\TranslationBundle\JMSTranslationBundle(),
            // new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new h4cc\AliceFixturesBundle\h4ccAliceFixturesBundle();
        }
        $thirdPartyDir = __DIR__.'/../thirdparty';
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        if ($fs->exists($thirdPartyDir)) {
            $finder = new \Symfony\Component\Finder\Finder();
            $finder->files()->in($thirdPartyDir);

            foreach ($finder as $file) {
                /** @var \Symfony\Component\Finder\SplFileInfo $file */
                $bundleConfig = json_decode(file_get_contents($file->getRealpath()), true);
                if ($bundleConfig) {
                    if (isset($bundleConfig['extra']) && isset($bundleConfig['extra']['bundle-class'])) {
                        if (class_exists($bundleConfig['extra']['bundle-class'])) {
                            $bundles[] = new $bundleConfig['extra']['bundle-class']();
                        }
                    }
                }
            }
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
