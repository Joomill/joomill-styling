<?php
/*
 *  package: System - Joomill Styling plugin
 *  copyright: Copyright (c) 2026. Jeroen Moolenschot | Joomill
 *  license: GNU General Public License version 3 or later
 *  link: https://www.joomill.nl
 */

namespace Joomill\Plugin\System\Joomill\Extension;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Event\Application\AfterRenderEvent;
use Joomla\CMS\Event\Application\BeforeCompileHeadEvent;
use Joomla\CMS\Event\Content\ContentPrepareEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\Event\SubscriberInterface;

class Joomill extends CMSPlugin implements SubscriberInterface
{
	protected $app;

	public function __construct(&$subject, array $config = array())
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Returns the events this plugin subscribes to.
	 *
	 * @return  array
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onBeforeCompileHead' => 'onBeforeCompileHead',
			'onAfterRender'       => 'onAfterRender',
			'onContentPrepare'    => 'onContentPrepare',
		];
	}

	public function onBeforeCompileHead(BeforeCompileHeadEvent $event)
	{
		if (!$this->app->isClient('administrator'))
		{
			return;
		}

		$wa = $this->app->getDocument()->getWebAssetManager();
		$wa->registerAndUseStyle('style', 'https://www.joomill.nl/customers/style.css');

		$currentURL    = Uri::getInstance();
		$currentDomain = $currentURL->toString(array('scheme', 'host'));
		if (strpos($currentDomain, "joomill.dev") !== false)
		{
			$wa->registerAndUseStyle('devstyle', 'https://www.joomill.nl/customers/style-dev.css');
		}
	}

	public function onAfterRender(AfterRenderEvent $event)
	{
		// Only for administrator
		if (!$this->app->isClient('administrator'))
		{
			return;
		}

		$body   = Factory::getApplication()->getBody();
		$find[] = Uri::getInstance()->root() . 'media/templates/administrator/atum/images/logos/login.svg';
		$find[] = Uri::getInstance()->root() . 'media/templates/administrator/atum/images/logos/brand-large.svg';
		$find[] = Uri::getInstance()->root() . 'media/templates/administrator/atum/images/logos/brand-small.svg';
		$find[] = 'href="index.php?option=com_cpanel&view=cpanel&dashboard=help"';
		$find[] = '<a href="https://docs.joomla.org/Special:MyLanguage/How_do_you_recover_or_reset_your_admin_password%3F" target="_blank" rel="noopener nofollow" title="Open Inloggegevens vergeten? in een nieuw venster">Inloggegevens vergeten?</a>';
		$find[] = '<a href="https://docs.joomla.org/Special:MyLanguage/How_do_you_recover_or_reset_your_admin_password%3F" target="_blank" rel="noopener nofollow" title="Open Forgot your login details? in new window">Forgot your login details?</a>';

		$replace[] = 'https://www.joomill.nl/customers/logo.svg';
		$replace[] = 'https://www.joomill.nl/customers/logo-wit.svg';
		$replace[] = 'https://www.joomill.nl/customers/blokjes-wit.svg';
		$replace[] = 'href="https://www.joomill.nl/contactgegevens" target="_blank"';
		$replace[] = '<a href="https://www.joomill.nl/contactgegevens" target="_blank" rel="noopener nofollow" title="Heb je een vraag?">Hulp nodig?</a>';
		$replace[] = '<a href="https://www.joomill.nl/contactgegevens" target="_blank" rel="noopener nofollow" title="Heb je een vraag?">Need help?</a>';

		$body = str_replace($find, $replace, $body);
		$this->app->setBody($body);
	}

	public function onContentPrepare(ContentPrepareEvent $event)
	{
		// Only for frontend
		if (!$this->app->isClient('site'))
		{
			return;
		}

		// SET ROBOTS META TAG ON DEV ONLY
		$currentURL    = Uri::getInstance();
		$currentDomain = $currentURL->toString(array('scheme', 'host'));
		if (strpos($currentDomain, "joomill.dev") !== false)
		{
			$this->app->getDocument()->setMetaData('robots','noindex, nofollow');
		}

		// SET THEME COLOR
		$themecolor = $this->params->get('themecolor', '#ffffff');
		$this->app->getDocument()->setMetaData('theme-color', $themecolor);

		// SET HOMEPAGE PAGETITLE
		$menu     = $this->app->getMenu();
		$language = $this->app->getLanguage();
		$siteName = $this->app->get('sitename');

		if ($menu->getActive() == $menu->getDefault($language->getTag()))
		{
			if ($this->params->get('homepagetitle'))
			{
				$this->app->getDocument()->setTitle($this->params->get('homepagetitle'));
			}
			else
			{
				$this->app->getDocument()->setTitle($siteName);
			}
		}

		// SET CANONICAL URL
		$link = Uri::getInstance()->toString();
		$link = rtrim($link, '/');
		$link = strtok($link, '?');
		$this->app->getDocument()->addHeadLink($link, 'canonical');
	}
}
