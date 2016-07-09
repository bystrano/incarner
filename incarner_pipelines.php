<?php
/**
 * Pipelines du plugin Incarner
 *
 * @plugin     Incarner
 * @copyright  2016
 * @author     Michel Bystranowski
 * @licence    GNU/GPL
 */

/**
 * Afficher un lien pour incarner un auteur sur sa page
 *
 * @pipeline affiche_gauche
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function incarner_boite_infos($flux) {

	if (($flux['args']['type'] === 'auteur')
			and (autoriser('incarner'))) {
		include_spip('base/abstract_sql');
		include_spip('inc/session');

		$id_auteur = $flux['args']['id'];

		if ($id_auteur != session_get('id_auteur')) {
			$login = sql_getfetsel(
				'login',
				'spip_auteurs',
				'id_auteur=' . intval($id_auteur)
			);
			$url_self = urlencode(self());
			$url_action = generer_url_action(
				'incarner',
				'login=' . $login . '&redirect=' . $url_self
			);

			$flux['data'] .= '<a href="' . $url_action . '">';
			$flux['data'] .= _T('incarner:incarner_login', array('login' => $login));
			$flux['data'] .= '</a>';
		}
	}

	return $flux;
}

/**
 * Ajouter un lien dans côté public pour redevenir webmestre
 *
 * @pipeline formulaire_admin
 * @param  array $html Données du pipeline
 * @return array       Données du pipeline
 */
function incarner_affichage_final($html) {

	if (! $cle_actuelle = $_COOKIE['spip_cle_incarner']) {
		return $html;
	}

	include_spip('inc/config');
	include_spip('inc/session');

	$cles = lire_config('incarner/cles') ? lire_config('incarner/cles') : array();

	if ((! $cles) or (! $id_auteur = array_search($cle_actuelle, $cles))
			or (session_get('id_auteur') == $id_auteur)) {
		return $html;
	}

	include_spip('base/abstract_sql');

	$login = sql_getfetsel(
		'login',
		'spip_auteurs',
		'id_auteur=' . intval($id_auteur)
	);

	$self = urlencode(self());
	$url = generer_url_action(
		'incarner',
		'login=' . $login . '&redirect=' . $self
	);

	$lien .= '<div class="menu-incarner" style="right: 60%;">';
	$lien .= '<a class="bouton-incarner" href="' . $url . '">';
	$lien .= _T('incarner:reset_incarner', array('login' => $login));
	$lien .= '</a></div>';

	$html = preg_replace('#(</body>)#', $lien . '$1', $html);

	return $html;
}

/**
 * Ajoute une feuille de styles à l'espace public
 *
 * @pipeline insert_head
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function incarner_insert_head($flux) {

	$flux .= '<link rel="stylesheet" type="text/css" href="' . find_in_path('css/incarner.css'). '" />';

	return $flux;
}
