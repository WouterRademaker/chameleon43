<?php
/**
 * File holding the Message class
 *
 * This file is part of the MediaWiki skin Chameleon.
 *
 * @copyright 2023, Morne Alberts
 * @license   GPL-3.0-or-later
 *
 * The Chameleon skin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by the Free
 * Software Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * The Chameleon skin is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @file
 * @ingroup Skins
 */

namespace Skins\Chameleon\Components;

/**
 * The Toc class.
 *
 * This component allows placing the TOC outside the main content.
 *
 * @author Morne Alberts
 * @since 4.4
 * @ingroup Skins
 */
class Toc extends Component {

	/**
	 * @inheritDoc
   * @param ChameleonTemplate $tpl
	 */
public function getHtml($tpl = null): string {
  if ( !is_null( $tpl ) ) {
  $this->setSkinTemplate( $tpl );
  }
		$html = $this->extractTocHtml();

		if ( $html === '' ) {
			return '';
		}

		$this->getSkin()->getOutput()->addBodyClasses( 'has-chameleon-toc' );

		// Add Top link.
		$html = substr_replace( $html, '<ul><li><a class="nav-link top" href="#">(Top)</a></li></ul><ul>', strpos( $html, '<ul>' ), 4 );

		// Add Bootstrap nav classes.
		$html = str_replace( '<ul>', '<ul class="nav">', $html );
		$html = str_replace( '<a href', '<a class="nav-link" href', $html );

		// Add collapsible button
		$count = 0;
		$html = preg_replace(
			'|(<li class="toclevel-1.*?)(<a.*?</a>[\s\n]*?)<ul class="|i',
			'$1<button class="btn toggle"><span class="toggle-icon"></span></button>$2<ul class="collapse ',
			$html,
			-1,
			$count
		);

		$hasCollapsibleClass = $count > 0 ? ' has-collapsible' : '';

		return '<div class="chameleon-toc-wrapper"><div class="chameleon-toc' . $hasCollapsibleClass . '">' . $html . '</div></div>';
	}

	/**
	 * @inheritDoc
	 */
	public function getResourceLoaderModules(): array {
		$modules = parent::getResourceLoaderModules();
		$modules[] = 'skin.chameleon.toc';
		return $modules;
	}

	private function extractTocHtml(): string {
		preg_match(
			'|<div id="toc"[\s\S]*?</div>[\s\S]*?</ul>[\s]*</div>|s',
			$this->getSkin()->getOutput()->getHTML(),
			$matches
		);

		return $matches[0] ?? '';
	}

}
