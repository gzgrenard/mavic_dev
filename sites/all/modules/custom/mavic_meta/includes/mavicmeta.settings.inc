<?php

/**
 * @file mavic_meta/includes/mavicmeta_settings.inc
 * Contains the variables and defaults used by mavicmedia : settings.
 */
/**
 * The variable namespace for mavicmeta
 */
define('MAVIC_META_NAMESPACE', 'mavic_meta__');

/**
 * Wrapper for variable_get() that uses the mavic_meta variable registry.
 *
 * @param string $name
 *  The variable name to retrieve. Note that it will be namespaced by
 *  pre-pending MAVIC_META_NAMESPACE, as to avoid variable collisions
 *  with other modules.
 * @param unknown $default
 *  An optional default variable to return if the variable hasn't been set
 *  yet. Note that within this module, all variables should already be set
 *  in the mavicmeta_variable_default() function.
 * @return unknown
 *  Returns the stored variable or its default.
 *
 * @see mavicmeta_variable_set()
 * @see mavicmeta_variable_del()
 * @see mavicmeta_variable_default()
 */
function mavicmeta_variable_get($name, $default = NULL) {
	// Allow for an override of the default.
	// Useful when a variable is required (like $path), but namespacing still desired.
	if (!isset($default)) {
		$default = mavicmeta_variable_default($name);
	}
	// Namespace all variables
	$variable_name = MAVIC_META_NAMESPACE . $name;
	return variable_get($variable_name, $default);
}

/**
 * Wrapper for variable_set() that uses the mavicmeta variable registry.
 *
 * @param string $name
 *  The variable name to set. Note that it will be namespaced by
 *  pre-pending MAVIC_META_NAMESPACE, as to avoid variable collisions with
 *  other modules.
 * @param unknown $value
 *  The value for which to set the variable.
 * @return unknown
 *  Returns the stored variable after setting.
 *
 * @see mavicmeta_variable_get()
 * @see mavicmeta_variable_del()
 * @see mavicmeta_variable_default()
 */
function mavicmeta_variable_set($name, $value) {
	$variable_name = MAVIC_META_NAMESPACE . $name;
	return variable_set($variable_name, $value);
}

/**
 * Wrapper for variable_del() that uses the mavicmeta variable registry.
 *
 * @param string $name
 *  The variable name to delete. Note that it will be namespaced by
 *  pre-pending MAVIC_META_NAMESPACE, as to avoid variable collisions with
 *  other modules.
 *
 * @see mavicmeta_variable_get()
 * @see mavicmeta_variable_set()
 * @see mavicmeta_variable_default()
 */
function mavicmeta_variable_del($name) {
	$variable_name = MAVIC_META_NAMESPACE . $name;
	variable_del($variable_name);
}

/**
 * The default variables within the mavicmeta namespace.
 *
 * @param string $name
 *  Optional variable name to retrieve the default. Note that it has not yet
 *  been pre-pended with the MAVIC_META_NAMESPACE namespace at this time.
 * @return unknown
 *  The default value of this variable, if it's been set, or NULL, unless
 *  $name is NULL, in which case we return an array of all default values.
 *
 * @see mavicmeta_variable_get()
 * @see mavicmeta_variable_set()
 * @see mavicmeta_variable_del()
 */
function mavicmeta_variable_default($name = NULL) {
	static $defaults;
	if (!isset($defaults)) {
		$langA = i18n_language_list();
		$defaults = array();
		$defaults['sync'] = array();
		$defaults['status'] = array();
		$defaults['defNid'] = array('en' => 202654, 'fr' => 202655, 'de' => 202656, 'it' => 202657, 'es' => 202659, 'ja' => 202658); //hard coded because nid of the frontPage
		$defaults['langs'] = array();
		foreach ($langA as $langs => $value) {
			$defaults['langs'][$langs] = array(
				'HP' => array(
					'name' => t('Home page (default)'),
					'type' => 'HP', // menu, content-type, node
					'lang' => 'multi', //single,
					'structure' => array(
						1 => array(),
					),
				),
			);
		}
	}

	if (!isset($name)) {
		return $defaults;
	}

	if (isset($defaults[$name])) {
		return $defaults[$name];
	}
}

/**
 * Return the fully namespace variable name.
 *
 * @param string $name
 *  The variable name to retrieve the namespaced name.
 * @return string
 *  The fully namespace variable name, prepended with
 *  MAVIC_META_NAMESPACE.
 */
function mavicmeta_variable_name($name) {
	return MAVIC_META_NAMESPACE . $name;
}

?>
