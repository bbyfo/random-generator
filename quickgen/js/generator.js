// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// generator.js
// written and released to the public domain by drow <drow@bin.sh>
// http://creativecommons.org/publicdomain/zero/1.0/

var gen_data = {};

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// generator function

function generate_text (type, presets) {
//console.log("presets top of generate_text()", presets)

  var list; if (list = gen_data[type]) {
      //console.log("list", list);
      if(presets){
      if(typeof presets[type] != 'undefined') {
        list = presets[type];
      }
    }else {
      presets = {};
    }
    var string; if (string = select_from(list, presets)) {
        return expand_tokens(string, presets);
      }
    }
    return '';
  }

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// generate multiple

function generate_list (type, n_of, presets) {
  //console.log("presets top of generate_list()", presets);
  var list = [];

  var i; for (i = 0; i < n_of; i++) {
    //console.log("presets being SENT TO >> generate_text", presets);
    list.push(generate_text(type, presets));
  }
  //console.log("list returned in generate_list", list);
  return list;
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// select from list

function select_from (list, presets) {
    if (list.constructor == Array) {
      return select_from_array(list);
    } else {
      return select_from_table(list);
    }
  }
  function select_from_array (list) {
    return list[Math.floor(Math.random() * list.length)];
  }
  function select_from_table (list) {
    var len; if (len = scale_table(list)) {
      var idx = Math.floor(Math.random() * len) + 1;

      var key; for (key in list) {
        var r = key_range(key);
        if (idx >= r[0] && idx <= r[1]) { return list[key]; }
      }
    }
    return '';
  }
  function scale_table (list) {
    var len = 0;

    var key; for (key in list) {
      var r = key_range(key);
      if (r[1] > len) { len = r[1]; }
    }
    return len;
  }
  function key_range (key) {
    var match; if (match = /(\d+)-00/.exec(key)) {
      return [ parseInt(match[1]), 100 ];
    } else if (match = /(\d+)-(\d+)/.exec(key)) {
      return [ parseInt(match[1]), parseInt(match[2]) ];
    } else if (key == '00') {
      return [ 100, 100 ];
    } else {
      return [ parseInt(key), parseInt(key) ];
    }
  }

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// expand {token} in string

function expand_tokens (string, presets) {
  var match; while (match = /{(\w+)}/.exec(string)) {
    var token = match[1];
    var repl; if (repl = generate_text(token, presets)) {
      string = string.replace('{'+token+'}',repl);
    } else {
      string = string.replace('{'+token+'}',token);
    }
  }
  return string;
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
