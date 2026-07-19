<?php

/**
 * Joomill Styling
 *
 * @copyright   Copyright (c) 2026 Jeroen Moolenschot | Joomill
 * @license     GNU General Public License version 3 or later; see LICENSE
 * @link        https://www.joomill-extensions.com
 */

// No direct access
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScript;

class plgSystemJoomillInstallerScript extends InstallerScript
{
	/**
	 * Minimum Joomla version required to install the extension.
	 *
	 * @var  string
	 */
	protected $minimumJoomla = '5.0';

	/**
	 * Minimum PHP version required to install the extension.
	 *
	 * @var  string
	 */
	protected $minimumPhp = '8.1';

	/**
	 * Runs after install or update. Enables the plugin on a fresh install.
	 *
	 * @param   string            $type    The action being performed (install, update, discover_install).
	 * @param   InstallerAdapter  $parent  The installer adapter.
	 *
	 * @return  boolean
	 */
	public function postflight($type, $parent)
	{
		if ($type === 'install')
		{
			$this->enablePlugin();
		}

		return true;
	}

	/**
	 * Enables the plugin in the #__extensions table.
	 *
	 * @return  void
	 */
	private function enablePlugin()
	{
		try
		{
			$db    = Factory::getContainer()->get('DatabaseDriver');
			$query = $db->getQuery(true)
				->update($db->quoteName('#__extensions'))
				->set($db->quoteName('enabled') . ' = ' . $db->quote(1))
				->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
				->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
				->where($db->quoteName('element') . ' = ' . $db->quote('joomill'));
			$db->setQuery($query);
			$db->execute();
		}
		catch (\Exception $e)
		{
			return;
		}
	}
}
