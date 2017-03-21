<?php
/**
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 *
 * Module.php
 * @data:       2017-02-08 19:36
 */

namespace DoctrineCacheToolbar;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\DependencyIndicatorInterface;
use Zend\Mvc\MvcEvent;
use Doctrine\ORM\Cache\Logging\StatisticsCacheLogger;

/**
 * Class Module
 * @package DoctrineCacheToolbar
 * @author: Szymon Michałowski <szmnmichalowski@gmail.com>
 */
class Module implements ConfigProviderInterface, DependencyIndicatorInterface
{
    /**
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        $app = $event->getApplication();
        $sharedEventManager = $app->getEventManager();
        $sharedEventManager->attach(MvcEvent::EVENT_DISPATCH,
            [$this, 'addCacheLogger'], 100);
    }

    /**
     * @param MvcEvent $event
     * @return MvcEvent
     */
    public function addCacheLogger(MvcEvent $event)
    {
        $app = $event->getApplication();
        $em = $app->getServiceManager()->get('Doctrine\ORM\EntityManager');
        $logger = new StatisticsCacheLogger();
        $config = $em->getConfiguration();
        $config->getSecondLevelCacheConfiguration()
            ->setCacheLogger($logger);

        return $event;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return include __DIR__.'/../config/module.config.php';
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleDependencies()
    {
        return ['ZendDeveloperTools', 'DoctrineORMModule'];
    }
}