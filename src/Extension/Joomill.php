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
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\Event\SubscriberInterface;

final class Joomill extends CMSPlugin implements SubscriberInterface
{
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
		];
	}

	public function onBeforeCompileHead(BeforeCompileHeadEvent $event)
	{
		$app      = $this->getApplication();
		$document = $event->getDocument();

		$currentDomain = Uri::getInstance()->toString(array('scheme', 'host'));
		$isDev         = strpos($currentDomain, 'joomill.dev') !== false;

		// Administrator: load the Joomill admin branding styles.
		if ($app->isClient('administrator'))
		{
			$wa = $document->getWebAssetManager();
			$wa->registerAndUseStyle('style', 'https://www.joomill.nl/customers/style.css');

			if ($isDev)
			{
				$wa->registerAndUseStyle('devstyle', 'https://www.joomill.nl/customers/style-dev.css');
			}

			return;
		}

		// Front-end head adjustments only.
		if (!$app->isClient('site'))
		{
			return;
		}

		// Block indexing on the dev domain.
		if ($isDev)
		{
			$document->setMetaData('robots', 'noindex, nofollow');
		}

		// Theme color metadata.
		$document->setMetaData('theme-color', $this->params->get('themecolor', '#ffffff'));

		// Override the homepage title.
		$menu = $app->getMenu();

		if ($menu->getActive() == $menu->getDefault($app->getLanguage()->getTag()))
		{
			$document->setTitle($this->params->get('homepagetitle') ?: $app->get('sitename'));
		}

		// Canonical URL.
		$link = strtok(rtrim(Uri::getInstance()->toString(), '/'), '?');
		$document->addHeadLink($link, 'canonical');
	}

	public function onAfterRender(AfterRenderEvent $event)
	{
		// Only for administrator
		if (!$this->getApplication()->isClient('administrator'))
		{
			return;
		}

		$body   = $this->getApplication()->getBody();
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
		$this->getApplication()->setBody($body);
	}
}
