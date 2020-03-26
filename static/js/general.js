/**
 * Created by jan on 11/03/2017.
 */
'use strict';

console.log(`\n %c yunshop  v1 %c https://www.yunz`+`shop.com \n\n`,"color: #fadfa3; background: #030307; padding:5px 0;","background: #fadfa3; padding:5px 0;");

$.locales       = {};
$.currentLocale = {};

/**
 * Check if given value is empty.
 *
 * @param  {any}  obj
 * @return {Boolean}
 */
function isEmpty(obj) {

  // null and undefined are "empty"
  if (obj == null) return true;

  // Assume if it has a length property with a non-zero value
  // that that property is correct.
  if (obj.length > 0)    return false;
  if (obj.length === 0)  return true;

  // If it isn't an object at this point
  // it is empty, but it can't be anything *but* empty
  // Is it empty?  Depends on your application.
  if (typeof obj !== "object") return true;

  // Otherwise, does it have any properties of its own?
  // Note that this doesn't handle
  // toString and valueOf enumeration bugs in IE < 9
  for (var key in obj) {
    if (hasOwnProperty.call(obj, key)) return false;
  }

  return true;
}

/**
 * Load current selected language.
 *
 * @return void
 */
function loadLocales() {
  for (lang in $.locales) {
    if (!isEmpty($.locales[lang])) {
      $.currentLocale = $.locales[lang] || {};
    }
  }
}

/**
 * Translate according to given key.
 *
 * @param  {string} key
 * @param  {dict}   parameters
 * @return {string}
 */
function trans(key, parameters = {}) {
  if (isEmpty($.currentLocale)) {
    loadLocales();
  }

  let segments = key.split('.');
  let temp = $.currentLocale || {};

  for (let  i in segments) {
    if (isEmpty(temp[segments[i]])) {
      return key;
    } else {
      temp = temp[segments[i]];
    }
  }

  for (let i in parameters) {
    if (!isEmpty(parameters[i])) {
      temp = temp.replace(':'+i, parameters[i]);
    }
  }

  return temp;
}

