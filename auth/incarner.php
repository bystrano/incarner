<?php

/* un fonction d'authentification qui ne vérifie rien, à part
   l'autorisation 'incarner' */
function auth_incarner_dist ($login, $pass, $serveur='', $phpauth=false) {

	// retrouver le login
	$login = auth_spip_retrouver_login($login);
	// login inconnu, n'allons pas plus loin
	if (!$login) return array();

  include_spip('inc/autoriser');
  if ( ! autoriser('incarner')) return array();

	$row = sql_fetsel("*", "spip_auteurs",
                    "login=" . sql_quote($login,$serveur,'text') .
                    " AND statut<>'5poubelle'",'','','','',$serveur);

	return $row;
}
