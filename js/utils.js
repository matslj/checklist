/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var utils = {
   /**
    * Extracts the value of a given (named) parameter from an url.
    * 
    * @param {type} name the name of the parameter whose value we are interested in
    * @param {type} href the url, with parameter, from wich we want to extract the parameter value
    * @returns {String} if parameter is present, return the parameter value. Otherwise return an empty string.
    */
    getParameterByName : function(name, href) {
        name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
        var regexS = "[\\?&]"+name+"=([^&#]*)";
        var regex = new RegExp( regexS );
        var results = regex.exec( href );
        if( results == null ) {
            return "";
        } else {
            return decodeURIComponent(results[1].replace(/\+/g, " "));
        }
    }
};
