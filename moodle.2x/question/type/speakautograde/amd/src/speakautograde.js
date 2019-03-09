// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the term of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * load the EnglishCentral player
 *
 * @module      qtype_speakautograde/view
 * @category    output
 * @copyright   Gordon Bateson
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since       Moodle 3.0
 */
define(["jquery", "core/str"], function($, STR) {

    /** @alias module:qtype_speakautograde/view */
    var SPEAK = {};

    // cache the plugin name and string cache
    SPEAK.plugin = "qtype_speakautograde";
    SPEAK.str = {};

    SPEAK.itemtype = "";
    SPEAK.itemmatch = "";
    SPEAK.editortype = "";

    SPEAK.editormaxtries = 50;
    SPEAK.editorinterval = 100; // 100 ms

    SPEAK.responsesample = "";
    SPEAK.responseoriginal = "";

    /*
     * initialize this AMD module
     */
    SPEAK.init = function(readonly, itemtype, editortype, responsesample) {

        // get RegExp expression for this item type
        var itemmatch = "";
        switch (itemtype) {
            case "chars": itemmatch = "."; break;
            case "words": itemmatch = "\\w+"; break;
            case "sentences": itemmatch = "[^\\.]+[\\.]"; break;
            case "paragraphs": itemmatch = "[^\\r\\n]+[\\r\\n]*"; break;
        }
        // take a look at https://github.com/RadLikeWhoa/Countable/blob/master/Countable.js
        // for more ideas on how to count chars, words, sentences, and paragraphs

        SPEAK.itemtype = itemtype;
        SPEAK.itemmatch = new RegExp(itemmatch, "g");
        SPEAK.editortype = editortype;

        if (readonly) {
            SPEAK.setup_response_heights();
        } else {
            SPEAK.setup_itemcounts();
            SPEAK.setup_responsesample(responsesample);
        }
    };

    SPEAK.setup_response_heights = function() {
       $("textarea.qtype_speak_response").each(function(){
           $(this).height(1);
           $(this).height(this.scrollHeight);
       });
    };

    SPEAK.setup_itemcounts = function() {
        $(".qtype_speak_response").each(function(){
            var id = SPEAK.get_itemcount_id(this);
            var editorloaded = $.Deferred();
            SPEAK.check_editor(this, editorloaded);
            $.when(editorloaded).done($.proxy(function(){
                SPEAK.create_itemcount(this, id);
                SPEAK.setup_itemcount(this, id);
            }, this, id));
        });

    };

    SPEAK.check_editor = function(response, editorloaded) {
        var selector = "";
        switch (SPEAK.editortype) {
            case "atto": selector = "[contenteditable=true]"; break;
            case "tinymce": selector = "iframe"; break;
        }
        if (selector=="") {
            // textarea - or unknown !!
            editorloaded.resolve();
        } else {
            var editorchecker = setInterval(function() {
                if ($(response).find(selector).length) {
                    clearInterval(editorchecker);
                    editorloaded.resolve();
                }
            }, SPEAK.editorinterval);
        }
    };

    SPEAK.create_itemcount = function(response, id) {
        if (document.getElementById(id)===null) {
            var p = document.createElement("P");
            p.setAttribute("id", id);
            p.setAttribute("class", "itemcount");
            response.parentNode.insertBefore(p, response.nextSibling);
        }
    };

    SPEAK.setup_itemcount = function(response, id) {
        var editable = SPEAK.get_editable_element(response);
        if (editable) {
            $(editable).keyup(function(){
                SPEAK.show_itemcount(this, id);
            });
            SPEAK.show_itemcount(editable, id);
        }
    };

    SPEAK.get_editable_element = function(response) {
        // search for plain text editor
        if ($(response).prop("tagName")=="TEXTAREA") {
            return response;
        }
        // search for Atto editor
        var editable = $(response).find("[contenteditable=true]");
        if (editable.length) {
            return editable.get(0);
        }
        // search for MCE editor
        var i = response.getElementsByTagName("IFRAME");
        if (i.length) {
            i = i[0];
            var d = (i.contentWindow || i.contentDocument);
            if (d.document) {
                d = d.document;
            }
            if (d.body && d.body.isContentEditable) {
                return d.body;
            }
        }
        // search for disabled text editor
        var editable = $(response).find("textarea");
        if (editable.length) {
            return editable.get(0);
        }
        // shouldn't happen !!
        return null;
    };

    SPEAK.get_textarea = function(response) {
        if ($(response).prop("tagName")=="TEXTAREA") {
            return response;
        }
        return $(response).find("textarea").get(0);
    };

    SPEAK.get_textarea_name = function(response) {
        var textarea = SPEAK.get_textarea(response);
        return $(textarea).attr("name");
    };

    SPEAK.get_itemcount_id = function(response) {
        var name = SPEAK.get_textarea_name(response);
        return "id_" + name + "_itemcount";
    };

    SPEAK.escape = function(id) {
        var regexp = new RegExp("(:|\\.|\\[|\\]|,|=|@)", "g");
        return "#" + id.replace(regexp, "\\$1");
    };

    SPEAK.show_itemcount = function(response, id) {
        if ($(response).prop("tagName")=="TEXTAREA") {
            var itemcount = $(response).val().match(SPEAK.itemmatch);
        } else {
            var itemcount = $(response).text().match(SPEAK.itemmatch);
        }
        if (itemcount) {
            itemcount = itemcount.length;
        } else {
            itemcount = 0;
        }

        // fetch descriptor string
        STR.get_strings([
            {"key": SPEAK.itemtype, "component": SPEAK.plugin}
        ]).done(function(s) {
            $(SPEAK.escape(id)).text(s[0] + ": " + itemcount);
        });
    };

    SPEAK.setup_responsesample = function(txt) {
        if (txt=='') {
            return;
        }
        SPEAK.responsesample = txt;
        STR.get_strings([
            {"key": "hidesample", "component": SPEAK.plugin},
            {"key": "showsample", "component": SPEAK.plugin}
        ]).done(function(s) {
            SPEAK.str.hidesample = s[0];
            SPEAK.str.showsample = s[1];
            var last = $(".qtext").find("p, div");
            if (last.length) {
                last = last.last();
            } else {
                last = $(".qtext");
            }
            last.append($("<span></span>").click(function(){
                var newtxt = "",
                    oldtxt = "",
                    saveresponse = false;
                if ($(this).hasClass("showsample")) {
                    $(this).removeClass("showsample")
                           .addClass("hidesample")
                           .text(SPEAK.str.hidesample);
                    newtxt = SPEAK.responsesample;
                    saveresponse = true;
                } else {
                    $(this).removeClass("hidesample")
                           .addClass("showsample")
                           .text(SPEAK.str.showsample);
                    newtxt = SPEAK.responseoriginal;
                }
                // Locate response element
                var r = $(this).closest(".qtext").next(".ablock").find(".answer .qtype_speak_response");
                var editor = null;
                if (r.is("[name$='_answer']")) {
                    // Plain text (i.e. no editor)
                    editor = r;
                } else {
                    // Atto
                    editor = r.find("[contenteditable=true]");
                    if (editor.length==0) {
                        // TinyMCE
                        editor = r.find("iframe").contents().find("[contenteditable=true]");
                        if (editor.length==0) {
                            // Plain text editor
                            editor = r.find("[name$='_answer']");
                        }
                    }
                }

                if (editor===null || editor.length==0) {
                    return false; // shouldn't happen !!
                }

                if (editor.prop("tagName")=="TEXTAREA") {
                    oldtxt = editor.val();
                    editor.val(newtxt).keyup();
                } else {
                    oldtxt = editor.text();
                    editor.text(newtxt).keyup();
                }

                if (saveresponse) {
                    SPEAK.responseoriginal = oldtxt;
                }
                return true;
            }).trigger("click"));
        });
    };

    return SPEAK;
});
