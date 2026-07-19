<?php

/**
 * Joomill Styling
 *
 * @copyright   Copyright (c) 2026 Jeroen Moolenschot | Joomill
 * @license     GNU General Public License version 3 or later; see LICENSE
 * @link        https://www.joomill-extensions.com
 */

// No direct access.
\defined('_JEXEC') or die;

use Joomill\Plugin\System\Joomill\Extension\Joomill;
use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;

return new class () implements ServiceProviderInterface {
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   4.3.0
	 */
	public function register(Container $container)
	{
		$container->set(
			PluginInterface::class,
			// Lazy loading (Joomla 6.1+): the plugin is only instantiated when needed.
			$container->lazy(Joomill::class, function (Container $container): PluginInterface {
				$plugin = new Joomill(
					$container->get(DispatcherInterface::class),
					(array) PluginHelper::getPlugin('system', 'joomill')
				);
				$plugin->setApplication(Factory::getApplication());

				return $plugin;
			})
		);
	}
};
